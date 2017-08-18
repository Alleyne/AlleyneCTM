<?php namespace App\library;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use App\library\Sity;
use App\library\Pant;
use App\Notifications\emailNuevaOcobro;
use Session, DB, Cach, Log, Cache;;

use App\Un;
use App\Prop;
use App\User;
use App\Ctdasm;
use App\Catalogo;
use App\Ctdiario;
use App\Detallepago;
use App\Pcontable;
use App\Ctmayore;
use App\Pago;
use App\Detallepagofactura;
use App\Diariocaja;
use App\Factura;
use App\Org;
use App\Calendarevento;
use App\Trantipo;

class Npagonoid {
  // Nuevo pago no identificado  
  
  /** 
  *=============================================================================================
  * Esta function comienza el proceso de contabilizar los pagos no identificados recibidos de
  * propietarios y al final del proceso notifica al propietario que su pago se ha contabilizado
  * con exito.
  *
  * @param  collection  $pago
  * @param  collection  $periodo
  * @return void
  *===========================================================================================*/
  public static function iniciaPago($pago, $periodo) {
    //dd($pago, $periodo);
    
    // procesa el pago recibido
    self::procesaPago($pago, $periodo);
    
    // procede a notificar al propietario la generacion de una nueva order de cobro
    
    // encuentra los datos para generar el estado de cuentas de un determinada unidad
    //$datos= Sity::getdataEcuenta($un_id);
    //dd($datos['imps']->toArray());    
    
/*    $props= Prop::where('un_id', $un_id)
           ->where('encargado', 1)
           ->join('users','users.id','=','props.user_id')
           ->select('users.id','email','nombre_completo')
           ->get();*/
    //dd($props->toArray());
    
    // envia email a cada uno de los propietarios de la unidad que sean encargados  
    /* foreach ($props as $prop) {
        Mail::to($prop->email, $prop->nombre_completo)
            ->queue(new sendnuevoEcuentas($datos['data'], $datos['imps'], $datos['recs']));
       }*/

    // envia una notificacion via email a cada uno de los propietarios de la unidad que sean encargados  
/*    foreach ($props as $prop) {
      $user= User::find($prop->id);
      //dd($user);
      $user->notify(new emailNuevaOcobro($pdo, $user->nombre_completo));      
    } */
  } // end function

  /** 
  *=============================================================================================
  * Este proceso se encarga de registrar el cobro por cuotas de mantenimiento o recargos,
  * también realiza los debidos asientos contables en cada una de las cuentas afectadas.
  * @param  collection  $pago
  * @param  collection  $periodo 
  * @return void
  *===========================================================================================*/
  
  public static function procesaPago($pago, $periodo) {
    //dd($pago, $periodo);

    // almacena el pago recibido en la variable $sobrante
    $sobrante = $pago->monto;

    //Prioridad no 1, verifica si hay cuotas regulares pendiente por pagar.
    $datos = self::cobraFacturas($pago, $sobrante, $periodo);
    $sobrante = round((float)$datos['sobrante'], 2);
    
    //Prioridad no 2, verifica si hay recargos pendiente por pagar.
    $datos = self::cobraRecargos($pago, $sobrante, $periodo);
    $sobrante = round((float)$datos['sobrante'], 2);
    
    //Prioridad no 3, verifica si hay cuotas extraordinarias por pagar.
    $datos = self::cobraCuotaExtraordinaria($pago, $sobrante, $periodo);
    $sobrante = round((float)$datos['sobrante'], 2);
    
    //Prioridad no 4, verifica si se trata de un pago anticipado con el proposito de obtener descuento
    $datos = Desc::verificaDescuento($pago, $sobrante, $periodo);
    $sobrante = round((float)$datos['sobrante'], 2);

    //Prioridad no 5, si al final de todo el proceso hay un sobrante entonces se registra como Pago anticipado
    // si sobra dinero y es fresco se tiene que registrar en el banco o caja geneal segun el tipo de pago que se hizo
    // si sobra dinero pero no es fresco no se tiene que hacer nada ya que ese dinero ya esta contabilizado en la cuenta de pagos anticipados

    if ($sobrante > 0) {
       $sobrante = self::registraSobranteFinal($pago, $sobrante, $periodo);
    }
  }
 

  /** 
  *=========================================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
  * @param  collection  $pago
  * @param  decimal     $sobrante
  * @param  collection  $periodo
  * @return array
  *=========================================================================================================*/
  public static function cobraFacturas($pago, $sobrante, $periodo) { 
    //dd($pago, $sobrante, $periodo);

    // inicializa variables
    $pago_id = $pago->id;
    $un_id = $pago->un_id;
    $montoRecibido = $sobrante;
    $f_pago = $pago->f_pago;
    $unCodigo = $pago->un->codigo;    
    $tipoPago = 4;
    $siglas = 'bl';
    $trans_no = $pago->trans_no;
    $periodo = $periodo->id;   
    $nota = ' '.$unCodigo.', Pago #'.$pago->id.' '.$siglas.'-'.$trans_no;
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
            ->where('un_id', $un_id)
            ->where('pagada', 0)
            ->orderBy('fecha', 'asc')
            ->get();
    //dd($datos->toArray());
  
    $regresa = array();

    // si hay cuotas de mantenimiento regulares por cobrar continua proceso, si no hay regresa
    // if #1
    if (!$datos->isEmpty()) { 
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa = Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);  
      // dd($saldocpa);

      // incializa variables a utilizar
      $cuenta_1 = Catalogo::find(1)->nombre;    // 1120.00 Cuotas de mantenimiento regulares por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_31 = Catalogo::find(31)->nombre;  // 2030.00 Pagos no indentificados

      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);
      
      foreach ($datos as $dato) {
        $importe = round((float)$dato->importe, 2);         
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        $ctdasm_id = $dato->id;

        // if #2
        //  Log::info([$montoRecibido, $dato->importe, $saldocpa]);          
        if (($montoRecibido + $saldocpa) >= $importe) {
          // hay suficiente dinero para pagar por lo menos una cuota regular
          // por lo tanto, registra la cuota regular como pagada
          $dto = ctdasm::find($dato->id);
          $dto->pagada = 1;
          $dto->save();  
        
          // if #3
          if ($montoRecibido >= $importe) {
            // se recibio suficiente dinero para pagar por lo menos una cuota,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $importe;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, 'Cobra cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $importe), 2);
            
            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $importe), 2);

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $importe;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $importe;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, 'Cobra cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota de mantenimiento extraordinaria de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $hayPie = true;
          
          } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - ($importe - $montoRecibido)), 2);
             
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = ($importe - $montoRecibido);
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, ($importe - $montoRecibido), $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = ($importe - $montoRecibido);
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, ($importe - $montoRecibido), $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, 'Cobra cuota de mant regular de '.$mesAnio.$nota, $importe, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular de '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($importe - $montoRecibido,2). ' para completar pago de cuota de mantenimiento regular de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible
            $montoRecibido = 0;    
            $hayPie = true;
          
          } else {
            return '<< ERROR >> en function cobraFacturas()';
          } // endif #3

        } // endif #2

      } // endforeach

      // agrega ultima linea al libro diario      
      if ($hayPie) {
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo;
        $diario->detalle = 'Para registrar cobro de cuota de mant regular, unidad'.$nota;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 
    
    } // endif #1
  
  } // end function
 
  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todos los recargos posibles dependiendo del monto disponible.
  * @param  collection  $pago
  * @param  decimal     $sobrante
  * @param  collection  $periodo
  * @return array
  *===========================================================================================*/
  public static function cobraRecargos($pago, $sobrante, $periodo) { 
    //dd($pago, $sobrante, $periodo);
    
    // inicializa variables
    $pago_id = $pago->id;
    $un_id = $pago->un_id;
    $montoRecibido = $sobrante;
    $f_pago = $pago->f_pago;
    $unCodigo = $pago->un->codigo;    
    $tipoPago = 4;
    $siglas = 'bl';
    $trans_no = $pago->trans_no;
    $periodo = $periodo->id;   
    $nota = ' '.$unCodigo.', Pago #'.$pago->id.' '.$siglas.'-'.$trans_no;  

    // Encuentra todos los recargos por pagar
    $datos = Ctdasm::where('pcontable_id','<=', $periodo)
             ->where('un_id', $un_id)
             ->whereDate('f_vencimiento','<', $f_pago)
             ->where('recargo_siono', 1)
             ->where('recargo_pagado', 0)
             ->get();
    //dd($datos->toArray());
  
    $regresa = array();

    // si hay recargos en cuotas de mantenimiento regulares por cobrar continua proceso, si no hay regresa
    // if #1
    if (!$datos->isEmpty()) { 
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa = Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);  
      // dd($saldocpa);

      // incializa variables a utilizar
      $cuenta_2 = Catalogo::find(2)->nombre;    // 1130.00 Recargo en cuota de mantenimiento por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_31 = Catalogo::find(31)->nombre;  // 2030.00 Pagos no indentificados

      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);
      
      foreach ($datos as $dato) {
        $recargo = round((float)$dato->recargo, 2); 
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        $ctdasm_id = $dato->id;
        
        // if #2
        //  Log::info([$montoRecibido, $dato->recargo, $saldocpa]);  
        if (($montoRecibido + $saldocpa) >= $recargo) {
          // hay suficiente dinero para pagar por lo menos un recargo
          // por lo tanto, registra el recargo como pagada
          $dto = ctdasm::find($dato->id);
          $dto->recargo_pagado = 1;
          $dto->save();  
          
          // if #3
          if ($montoRecibido >= $recargo) {
            // se recibio suficiente dinero para pagar por lo menos un recargo,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados
            
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $recargo;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para recargo en mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2.', '.$dato->mesAnio;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, 'Cobra recargo en cuota de mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento regular de '. $mesAnio, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $recargo), 2);

            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $recargo), 2);

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $recargo;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para recargo en cuota de mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $recargo;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2.', '.$dato->mes_anio;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para completar recargo en mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, 'Cobra recargo en cuota de mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($recargo),2). ' para completar pago de recargo en cuota de mantenimiento regular de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $recargo;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $hayPie = true;

          } elseif ($montoRecibido < $recargo) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - ($recargo - $montoRecibido)), 2);
            
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = ($recargo - $montoRecibido);
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, ($recargo - $montoRecibido), $un_id, $pago_id);       
             
            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = ($recargo - $montoRecibido);
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para completar recargo en mant regular de '.$mesAnio.$nota, ($recargo - $montoRecibido), $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, 'Cobra recargo en cuota de mant regular de '.$mesAnio.$nota, $recargo, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento regular de '. $mesAnio, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($recargo - $montoRecibido,2). ' para completar pago de recargo en cuota de mantenimiento regular de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible
            $montoRecibido = 0;    
            $hayPie = true;

          } else {
            return '<< ERROR >> en function cobraRecargos()';
          } // endif #3

        } // endif #2

      } // endforeach

      if ($hayPie) {
        // agrega ultima linea al libro diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo;
        $diario->detalle = 'Para registrar cobro de recargo en cuota de mant regular, unidad'.$nota;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 
    
    } // endif #1
  
  } // end function

  /** 
  *==================================================================================================================
  * Este proceso se encarga de cobrar todas las cuotas extraordinarias posibles dependiendo del monto disponible.
  * @param  collection  $pago
  * @param  decimal     $sobrante
  * @param  collection  $periodo 
  * @return array
  *==================================================================================================================*/
  public static function cobraCuotaExtraordinaria($pago, $sobrante, $periodo) { 
    //dd($pago, $sobrante, $periodo);

    // inicializa variables
    $pago_id = $pago->id;
    $un_id = $pago->un_id;
    $montoRecibido = $sobrante;
    $f_pago = $pago->f_pago;
    $unCodigo = $pago->un->codigo;    
    $tipoPago = 4;
    $siglas = 'bl';
    $trans_no = $pago->trans_no;
    $periodo = $periodo->id;   
    $nota = ' '.$unCodigo.', Pago #'.$pago->id.' '.$siglas.'-'.$trans_no;
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
          ->where('un_id', $un_id)
          ->where('extra_siono', 1)
          ->where('extra_pagada', 0)
          ->orderBy('fecha', 'asc')
          ->get();
    //dd($datos->toArray());

    $regresa = array();

    // si hay cuotas de mantenimiento extraordinarias por cobrar continua proceso, si no hay regresa
    // if #1
    if (!$datos->isEmpty()) { 
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa = Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);  
      // dd($saldocpa);

      // incializa variables a utilizar
      $cuenta_16 = Catalogo::find(16)->nombre;  // 1110.00 Cuotas de mantenimiento extraordinarias por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_31 = Catalogo::find(31)->nombre;  // 2030.00 Pagos no indentificados

      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);

      foreach ($datos as $dato) {
        $extra = round((float)$dato->extra, 2); 
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        $ctdasm_id = $dato->id;
        
        // if #2
        //  Log::info([$montoRecibido, $dato->extra, $saldocpa]);  
        if (($montoRecibido + $saldocpa) >= $extra) {
          // hay suficiente dinero para pagar por lo menos una cuota extraordinaria
          // por lo tanto, registra la cuota extraordinaria como pagada
          $dto = ctdasm::find($dato->id);
          $dto->extra_pagada= 1;
          $dto->save();  

          // if #3
          if ($montoRecibido >= $extra) {
            // se recibio suficiente dinero para pagar por lo menos una cuota,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $extra;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, $extra, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $extra;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1110.00 "Cuotas de mantenimiento extraordinarias por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, 'Cobra cuota de mant extraordinaria de '.$mesAnio.$nota, $extra, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria '. $mesAnio, $dato->id, $extra, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $extra), 2);

            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $extra), 2);

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = $extra;
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, $extra, $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $extra;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $extra;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para completar cuota de mant extraordinaria de '.$mesAnio.$nota, $extra, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, 'Cobra cuota de mant extraordinaria de '.$mesAnio.$nota, $extra, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($extra),2). ' para completar pago de la cuota de mantenimiento extraordinaria de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $extra;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $hayPie = true;

          } elseif ($montoRecibido < $extra) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = round(($saldocpa - ($extra - $montoRecibido)), 2);

            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            if ($i == 0) { $diario->fecha = $f_pago; }
            $diario->detalle = 'Pagos no indentificados';
            $diario->debito  = ($extra - $montoRecibido);
            $diario->save();
            
            $i = 1;
            $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
            
            // registra en el mayor
            // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
            Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Descuenta para cuota de mant regular de '.$mesAnio.$nota, ($extra - $montoRecibido), $un_id, $pago_id);       

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = ($extra - $montoRecibido);
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1110.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $extra;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta para completar cuota de mant extraordinaria de '.$mesAnio.$nota, ($extra - $montoRecibido), $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, 'Cobra cuota de mant extraordinaria de '.$mesAnio.$nota, $extra, $un_id, $pago_id, Null, Null, $ctdasm_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria', $dato->id, $extra, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($extra - $montoRecibido,2). ' para completar pago de cuota de mantenimiento extraordinaria de '.$mesAnio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible
            $montoRecibido = 0;    
            $hayPie = true;
          
          } else {
            return '<< ERROR >> en function cobraCuotaExtraordinaria()';
          } // endif #3

        } // endif #2

      } // endforeach
      
      if ($hayPie) {
        // agrega ultima linea al libro diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo;
        $diario->detalle = 'Para registrar cobro de cuota de mant extraordinaria, unidad'.$nota;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;

      return $regresa; 
    
    } // endif #1
  
  } // end function
  
  /** 
  *=============================================================================================
  * Registra el sobrante final si existe
  * @param  collection  $pago
  * @param  decimal     $sobrante
  * @param  collection  $periodo
  * @return void
  *===========================================================================================*/
  public static function registraSobranteFinal($pago, $sobrante, $periodo) {
    //dd($pago, $sobrante, $periodo);

    // inicializa variables
    $pago_id = $pago->id;
    $un_id = $pago->un_id;
    $f_pago = $pago->f_pago;
    $unCodigo = $pago->un->codigo;    
    $tipoPago = 4;
    $siglas = 'bl';
    $trans_no = $pago->trans_no;
    $periodo = $periodo->id;   
    $nota = ' '.$unCodigo.', Pago #'.$pago->id.' '.$siglas.'-'.$trans_no;
    
    // incializa variables a utilizar
    $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios

    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //dd($saldocpa);

    // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->fecha = $f_pago;
    $diario->detalle = 'Pagos no indentificados';
    $diario->debito  = $sobrante;
    $diario->save();
    
    $nota = ' '.$unCodigo.', Pago #'.$pago_id.' bl-'.$trans_no;
    
    // registra en el mayor
    // registra una disminucion en la cuenta 31 2030.00 Pagos no indentificados 
    Sity::registraEnCuentas($periodo, 'menos', 2, 31, $f_pago, 'Trasfiere monto a Cuenta de pagos anticipados de '.$unCodigo, $sobrante, $un_id, $pago_id);           

    // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->detalle = $cuenta_5;
    $diario->debito = Null;
    $diario->credito = $sobrante;
    $diario->save();

    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->detalle = 'Para registrar pago anticipado, unidad'.$nota;
    $diario->save();

    // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"
    Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, $cuenta_5.' '.$nota, $sobrante, $un_id, $pago_id); 
  
    // salva un nuevo registro que representa una linea del recibo
    $dto = new Detallepago;
    $dto->pcontable_id = $periodo;
    $dto->detalle = 'Estimado propietario, al contabilizar su pago, el sistema ha detectado que existe un sobrante de B/. '.number_format($sobrante,2). ', por lo tanto el mismo será depositado en su cuenta de pagos anticipados. Este saldo usted lo podrá utilizar para completar futuros pagos. El nuevo saldo de su cuenta de pagos anticipados a la fecha es de B/.'.number_format(($saldocpa+$sobrante),2);
    $dto->monto = $sobrante;
    $dto->un_id = $un_id;
    $dto->tipo = 4;
    $dto->pago_id = $pago_id;
    $dto->save();
  } 


  /** 
  *=============================================================================================
  * Anula un pago efectuado con cheque
  * @param  integer $pago_id        
  * @return void
  *===========================================================================================*/
  public static function anulaPagoCheque($pago_id) {
    dd('npago 680...',$pago_id);
    $pago=Pago::find($pago_id);
    $pago->anulado = 1;
    $pago->entransito = 0;
    $pago->save(); 
    return; 
  }
  
  /** 
  *=============================================================================================
  * Anula un pago
  * @param  string $pago_id   "2"     
  * @return void
  *===========================================================================================*/
   public static function anulaPago($pago_id) {
    //dd($pago_id);
    
    // anula el pago en la tabla pagos
    $pago = Pago::find($pago_id);
    $pago->anulado = 1;
    $pago->save(); 

    // Registra en bitacoras
    Sity::RegistrarEnBitacora($pago, Null, 'Pago', 'Anula pago de propietario');   
    
    $f_pago = Carbon::parse($pago->f_pago);
    
    // determina cuantos periodos contables fueron afectados por el presente pago
    $periodos = Ctmayore::where('pago_id', $pago_id)
                ->select('pcontable_id')
                ->get();
    
    $periodos = $periodos->unique('pcontable_id');    
    //dd($detalles->toArray()); 

    foreach ($periodos as $periodo) {
      $debitos = Ctmayore::where('pago_id', $pago_id)
                  ->where('pcontable_id', $periodo->pcontable_id)
                  ->where('debito', 0)
                  ->get();
      // dd($detalles->toArray());
      
      $creditos = Ctmayore::where('pago_id', $pago_id)
                  ->where('pcontable_id', $periodo->pcontable_id)
                  ->where('credito', 0)
                  ->get();
      //dd($detalles->toArray());

      // primero anula todas las transacciones que tuvieron saldo debito igual a cero en el periodo en estudio
      $i=1;
      foreach ($debitos as $debito) {
        // agrega el nuevo registro al modelo ctmayore
        $dato = new Ctmayore;
        $dato->pcontable_id     = $periodo->pcontable_id;
        $dato->tipo             = $debito->tipo;
        $dato->cuenta           = $debito->cuenta;
        $dato->codigo           = $debito->codigo;
        $dato->fecha            = Carbon::today();
        $dato->detalle          = '(Anula) '.$debito->detalle;
        $dato->debito           = $debito->credito;
        $dato->credito          = 0;
        $dato->un_id            = $debito->un_id;
        $dato->pago_id          = $debito->pago_id; 
        $dato->save();

        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->pcontable_id;;
        if ($i == 1) {
          $diario->fecha  = Carbon::today();
        }       
        $diario->detalle = $debito->detalle;
        $diario->debito = $debito->credito;
        $diario->save();
        $i = 0;
      }
        
      // segundo anula todas las transacciones que tuvieron saldo credito igual a cero
      foreach ($creditos as $credito) {
        // agrega el nuevo registro al modelo ctmayore
        $dato = new Ctmayore;
        $dato->pcontable_id     = $periodo->pcontable_id;
        $dato->tipo             = $credito->tipo;
        $dato->cuenta           = $credito->cuenta;
        $dato->codigo           = $credito->codigo;
        $dato->fecha            = Carbon::today();
        $dato->detalle          = '(Anula) '.$credito->detalle;
        $dato->debito           = 0;
        $dato->credito          = $credito->debito;
        $dato->un_id            = $credito->un_id;
        $dato->pago_id          = $credito->pago_id; 
        $dato->save();

        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id  = $credito->pcontable_id;
        if ($i == 1) {
          $diario->fecha  = Carbon::today();
        }  
        $diario->detalle = $credito->detalle;
        $diario->credito = $credito->debito;
        $diario->save();
      }

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id = $periodo->pcontable_id;
      $diario->detalle = 'Para anular Pago No. '.$pago_id;
      $diario->save();       
    }
      
    // tercero, si con el pago se logro pagar una o mas cuotas de mantenimiento,
    // entonces se procede a revertir en la tabla ctdasms
    $datos = Ctmayore::where('pago_id', $pago_id)
                ->where('cuenta', 1)
                ->select('ctdasm_id')
                ->get();
    
    $datos = $datos->unique('ctdasm_id');    
    //dd($detalles->toArray());

    foreach ($datos as $dato) {
      Ctdasm::where('id', $dato->ctdasm_id)
            ->update(['pagada' => 0]);
    }

    // cuarto, si con el pago se logro pagar uno  o mas recargos en cuotas de mantenimiento,
    // entonces se procede a revertir en la tabla ctdasms
    $datos = Ctmayore::where('pago_id', $pago_id)
                ->where('cuenta', 2)
                ->select('ctdasm_id')
                ->get();
    
    $datos = $datos->unique('ctdasm_id');    
    //dd($detalles->toArray());

    foreach ($datos as $dato) {
      Ctdasm::where('id', $dato->ctdasm_id)
            ->update(['recargo_siono' => 1,  'recargo_pagado' => 0]);
    }

    return; 
  }

  /** 
  *=============================================================================================
  * Esta function penaliza individual por fecha de pago
  * @param  string      $f_pago  "2016-02-04"  - fecha en que se efectuo el pago  
  * @param  string      $un_id        "1"      - unidad que se desea penalizar individualmente
  * @param  integer     $periodo_id   "1"      - periodo mas antiguo abierto
  * @return void
  *===========================================================================================*/
  public static function penalizarTipo2($f_pago, $un_id, $periodo_id) {
    //dd($f_pago, $un_id, $periodo_id);
    
    // penaliza individualmente a una determinada unidad
    $datos= Ctdasm::where('un_id', $un_id)
                ->whereDate('f_vencimiento','<', $f_pago)
                ->where('pagada', 0)
                ->where('recargo_siono', 0)
                ->get();
    //dd($datos->toArray(), $f_pago, $un_id, $periodo_id); 

    $i = 1;   
    
    // inicializa variable para almacenar el total de recargos
    $totalRecargos= 0;       

    if ($datos->count()) {
      foreach ($datos as $dato) {
        $dto = Ctdasm::find($dato->id);
        $dto->recargo_siono = 1;
        $dto->save();  

        // acumula el total de recargos
        $totalRecargos = $totalRecargos + $dato->recargo;
        
        // registra 'Recargo por cobrar en cuota de mantenimiento' 1130.00
        Sity::registraEnCuentas(
              $periodo_id,
              'mas',
              1,
              2, //'1130.00'
              Carbon::parse($dato->f_vencimiento)->addDay(),
              'Recargo en cuota de mant por cobrar unidad '.$dato->ocobro,
              $dato->recargo,
              $dato->un_id,
              Null,
              Null,
              Null,
              $dato->id,
              Null
             );

        // registra 'Ingreso por cuota de mantenimiento' 4130.00
        Sity::registraEnCuentas(
              $periodo_id,
              'mas',
              4,
              4, //'4130.00'
              Carbon::parse($dato->f_vencimiento)->addDay(),
              '   Ingreso por recargo en cuota de mant unidad '.$dato->ocobro,
              $dato->recargo,
              $dato->un_id,
              Null,
              Null,
              Null,
              $dato->id,
              Null
             );

        // registra resumen de la facturacion mensual en Ctdiario principal 
        if ($i == 1) {
          // registra en Ctdiario principal
          $dto = new Ctdiario;
          $dto->pcontable_id = $periodo_id;
          $dto->fecha   = Carbon::parse($dato->f_vencimiento)->addDay();
          $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
          $dto->debito  = $dato->recargo; 
          $dto->save(); 
        
        } else {
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id  = $periodo_id;
            $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
            $dto->debito  = $dato->recargo;
            $dto->save(); 
        }
        $i++;
      } // end foreach $datos 
      
      // registra en Ctdiario principal
      $dto = new Ctdiario;
      $dto->pcontable_id = $periodo_id;
      $dto->detalle = '   '.Catalogo::find(4)->nombre;
      $dto->credito = $totalRecargos;
      $dto->save(); 

      // registra en Ctdiario principal
      $dto = new Ctdiario;
      $dto->pcontable_id = $periodo_id;
      $dto->detalle = 'Para registrar resumen de recargos en cuotas de mant por cobrar vencidas a '.Date::parse($dato->f_vencimiento)->toFormattedDateString();
      $dto->save();     
     
      $totalRecargos= 0;
    } // end of if
  } // end of function

  /** 
  *=============================================================================================
  * Esta function penaliza individual por fecha de pago
  * @param  integer $periodo  2
  * @param  string $ocobro    "1A-T100R2 Jun-2016"
  * @param  string $detalle   "Paga cuota de mantenimiento de Jun-2016 por anticipado"
  * @param  integer $ref      4
  * @param  decimal $monto    95.0
  * @param  string $un_id     "1"
  * @param  integer $pago_id  8
  * @param  integer $no       1
  * @param  integer $tipo     1 
  * @return void
  *===========================================================================================*/
  public static function registraDetallepago($periodo, $ocobro, $detalle, $ref, $monto, $un_id, $pago_id, $no, $tipo) {
    //dd($periodo, $ocobro, $detalle, $ref, $monto, $un_id, $pago_id, $no, $tipo);
    // salva un nuevo registro que representa una linea del recibo
    $dato = new Detallepago;
    $dato->no = $no;
    $dato->pcontable_id = $periodo;
    $dato->ocobro = $ocobro;
    $dato->detalle = $detalle;
    $dato->ref = $ref;
    $dato->monto = number_format($monto,2);
    $dato->un_id = $un_id;
    $dato->pago_id = $pago_id;
    $dato->tipo = $tipo;
    $dato->save();
  }

  /** 
  *=============================================================================================
  * Encuentra el ultimo renglon de un determinado pago
  * @param  integer $pago_id 7       
  * @return void
  *===========================================================================================*/
  public static function getLastNoDetallepago($pago_id) {
    //dd($pago_id);
    
    $dato = Detallepago::where('pago_id', $pago_id)
                       ->orderBy('no', 'desc')
                       ->first();
    $no = ($dato) ? floatval($dato->no) + 1 : 1;
    return $no;
  } 

  /** 
  *=============================================================================================
  *  Esta function registra en libros el pago de una factura ya se parcial o completa
  * @param  string detallepagofactura_id  '7'       
  * @return void
  *===========================================================================================*/
  public static function contabilizaDetallePagoFactura($detallepagofactura_id, $periodo) {
  
    //dd($detallepagofactura_id, $periodo);

    // incializa variables a utilizar
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
    $cuenta_6 = Catalogo::find(6)->nombre;    // 2001.00 Cuentas por pagar a proveedores
    $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional

    // encuentra los datos del detalle de pago en estudio
    $dato = Detallepagofactura::find($detallepagofactura_id);
    //dd($dato->toArray());
    
    // verifica si existe registro para informe de diario de caja para el dia de hoy,
    // si no existe entonces lo crea.
    if ($dato->trantipo_id != '4' ) {
      $diariocaja= Diariocaja::where('fecha', $dato->fecha)->first();

      if (!$diariocaja) {
        $dto = new Diariocaja; 
        $dto->fecha = $dato->fecha;         
        $dto->save();
      }
    }  
      
    // encuentra los datos de la factura
    $factura = Factura::find($dato->factura_id);
    //dd($factura);
    
    // almacena el total de la factura 
    $totalfactura = round(floatval($factura->total),2);
    
    // encuentra los datos de la organizacion
    $org = Org::find($factura->org_id);
    //dd($org->toArray());    

    // verifica si se trata de un pago en efectivo
    if ($dato->pagotipo == 5) {
      $nota = $dato->pagotipo.' '.$dato->trantipo->siglas.' de la factura #'.$factura->doc_no.' '.$factura->afavorde;
    
    } else {
      $nota = $dato->pagotipo.' '.$dato->trantipo->siglas.'-'.$dato->doc_no.' de la factura #'.$factura->doc_no.' '.$factura->afavorde;
    } 
    //dd($nota);
    
    // registra en ctmayores una disminucion en la cuenta de Cuetas por pagar a proveedores
    Sity::registraEnCuentas(
      $periodo->id,
      'menos', 
      2,
      6,
      $dato->fecha,
      'Pago '.$nota,
      $dato->monto,
      Null,
      Null,
      $dato->id,
      $org->id,
      Null,
      Null
    );

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo->id;
    $diario->fecha   = $dato->fecha;
    $diario->detalle = $cuenta_6;
    $diario->debito  = $dato->monto;
    $diario->save(); 
    
    if ($dato->trantipo_id != '4' ) {
      // registra en ctmayores una disminucion en la cuenta Banco
      Sity::registraEnCuentas(
        $periodo->id,
        'menos',
        1, 
        32,
        $dato->fecha,
        'Pago '.$nota,
        $dato->monto,
        Null,
        Null,
        $dato->id,
        $org->id,
        Null,
        Null
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo->id;
      $diario->detalle = $cuenta_32;
      $diario->credito = $dato->monto;
      $diario->save(); 
    
    } else {
      // registra en ctmayores una disminucion en la cuenta Banco
      Sity::registraEnCuentas(
        $periodo->id,
        'menos',
        1, 
        8,
        $dato->fecha,
        'Pago '.$nota,
        $dato->monto,
        Null,
        Null,
        $dato->id,
        $org->id,
        Null,
        Null
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo->id;
      $diario->detalle = $cuenta_8;
      $diario->credito = $dato->monto;
      $diario->save(); 
    }
    
    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id = $periodo->id;
    $diario->detalle = 'Para registrar pago '.$nota;
    $diario->save(); 

    // registra el detalle de pago de factura como pagado contabilizado  
    $dato->etapa = 2;
    $dato->save();
    
    // verifica si hay algun detalle que no ha sido pagado contabilizado
    $sinContabilizar = Detallepagofactura::where('factura_id', $factura->id)
                    ->where('etapa', 1)
                    ->count('id');       
    //dd($sinContabilizar);
         
    // calcula el monto total de los detalles de la presente factura
    $totaldetalles = Detallepagofactura::where('factura_id', $factura->id)->where('etapa', 2)->sum('monto');       
    $totaldetalles = round(floatval($totaldetalles),2);
    //dd($totaldetalles, $sinContabilizar, $totalfactura, $factura->id);  

    // si el total de la factura es igual al total de los detalles y no exiten detalles por contabilizar
    // entonces registra la factura como pagada en su totalidad
    if (($totalfactura == $totaldetalles) && $sinContabilizar == 0) {
      $factura->totalpagodetalle = $totaldetalles;
      $factura->pagada = 1;
      $factura->save();   
      
    } elseif ($totaldetalles < $totalfactura) {
      $factura->totalpagodetalle = $totaldetalles;
      $factura->pagada = 0;
      $factura->save();
    }
  }

  /** 
  *=============================================================================================
  *  Esta function registra en libros el pago por reserva de Area social & BB
  * @param  string detallepagofactura_id  '7'       
  * @return void
  *===========================================================================================*/
  public static function contabilizaReservaAm($evento, $pago_id, $periodo) {
  
    //dd($evento, $periodo);

    // incializa variables a utilizar
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
    $cuenta_39 = Catalogo::find(39)->nombre;  // 2040.00 Depositos por reservacion de Area social & BB
    $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
    
    // determina el tipo de pago en siglas
    $tipoPago = Trantipo::find($evento->res_tipopago)->siglas;
    
    // determina el codigo de la unidad
    $unCodigo = Un::find($evento->un_id)->codigo;
    
    if ($evento->res_tipopago == '4' ) {    // sea banca en linea
      // registra en ctmayores un aumento en banco
      Sity::registraEnCuentas(
        $periodo,
        'mas',
        1, 
        8,
        $evento->res_fechapago,
        'Reservacion de '.$evento->title.', '.$unCodigo,
        $evento->res_monto,
        $evento->un_id,
        $pago_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $evento->res_fechapago;
      $diario->detalle = $cuenta_8;
      $diario->debito = $evento->res_monto;
      $diario->save(); 

    } else {  
      // verifica si existe registro para informe de diario de caja para el dia de hoy,
      // si no existe entonces lo crea.      
      $diariocaja= Diariocaja::where('fecha', $evento->res_fechapago)->first();

      if (!$diariocaja) {
        $dto = new Diariocaja; 
        $dto->fecha = $evento->res_fechapago;         
        $dto->save();
      }
      
      // registra en ctmayores un aumento en la Caja general
      Sity::registraEnCuentas(
        $periodo,
        'mas',
        1, 
        32,
        $evento->res_fechapago,
        'Reservacion de '.$evento->title.', '.$unCodigo,
        $evento->res_monto,
        $evento->un_id,
        $pago_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $evento->res_fechapago;
      $diario->detalle = $cuenta_32;
      $diario->debito = $evento->res_monto;
      $diario->save();        
    } 

    // registra en ctmayores un aumento cuenta Depositos por reservacion de amenidades
    Sity::registraEnCuentas(
      $periodo,
      'mas',
      2, 
      39,
      $evento->res_fechapago,
      'Reservacion de '.$evento->title.', '.$unCodigo.', pago #'.$pago_id.', '.$tipoPago.', doc #'.$evento->res_docno,
      $evento->res_monto,
      $evento->un_id,
      $pago_id
    );

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->detalle = $cuenta_39.', '.$unCodigo;
    $diario->credito = $evento->res_monto;
    $diario->save();       

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id = $periodo;
    $diario->detalle = 'Para registrar reservacion de '.$evento->title.', pago #'.$pago_id.', '.$tipoPago.', doc #'.$evento->res_docno;
    $diario->save(); 
    
    // Registra en Detallepago para generar un renglon en el recibo
    Self::registraDetallepago($periodo, '', 'Paga deposito por reservacion de '.$evento->title, '', $evento->res_monto, $evento->un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);  

  }

  /** 
  *=============================================================================================
  *  Esta function registra en libros el pago por reserva de Area social & BB
  * @param  string detallepagofactura_id  '7'       
  * @return void
  *===========================================================================================*/
  public static function contabilizaAlquilerAm($evento, $pago_id, $periodo) {
  
    //dd($evento, $periodo);

    // incializa variables a utilizar
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
    $cuenta_45 = Catalogo::find(45)->nombre;  // 4161.00 Ingresos por alquiler de Area social & BB
    $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
    
    // determina el tipo de pago en siglas
    $tipoPago = Trantipo::find($evento->pc_tipopago)->siglas;
    
    // determina el codigo de la unidad
    $unCodigo = Un::find($evento->un_id)->codigo;
    
    if ($evento->pc_tipopago == '4' ) {   // sea banca en linea
      // registra en ctmayores un aumento en banco
      Sity::registraEnCuentas(
        $periodo,
        'mas',
        1, 
        8,
        $evento->pc_fechapago,
        'Alquiler de '.$evento->title.', '.$unCodigo,
        $evento->pc_monto,
        $evento->un_id,
        $pago_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $evento->pc_fechapago;
      $diario->detalle = $cuenta_8;
      $diario->debito = $evento->pc_monto;
      $diario->save(); 
    
    } else {  
      // verifica si existe registro para informe de diario de caja para el dia de hoy,
      // si no existe entonces lo crea.      
      $diariocaja= Diariocaja::where('fecha', $evento->pc_fechapago)->first();

      if (!$diariocaja) {
        $dto = new Diariocaja; 
        $dto->fecha = $evento->pc_fechapago;         
        $dto->save();
      }
      
      // registra en ctmayores un aumento en la Caja general
      Sity::registraEnCuentas(
        $periodo,
        'mas',
        1, 
        32,
        $evento->pc_fechapago,
        'Alquiler de '.$evento->title.', '.$unCodigo,
        $evento->pc_monto,
        $evento->un_id,
        $pago_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $evento->pc_fechapago;
      $diario->detalle = $cuenta_32;
      $diario->debito = $evento->pc_monto;
      $diario->save();       
    } 

    // registra en ctmayores un aumento cuenta Depositos por reservacion de amenidades
    Sity::registraEnCuentas(
      $periodo,
      'mas',
      4, 
      45,
      $evento->pc_fechapago,
      'Alquiler de '.$evento->title.', '.$unCodigo.', pago #'.$pago_id.', '.$tipoPago.', doc #'.$evento->pc_docno,
      $evento->pc_monto,
      $evento->un_id,
      $pago_id
    );

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->detalle = $cuenta_45.', '.$unCodigo;
    $diario->credito = $evento->pc_monto;
    $diario->save();       

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id = $periodo;
    $diario->detalle = 'Para registrar alquiler de '.$evento->title.', pago #'.$pago_id.', '.$tipoPago.', doc #'.$evento->pc_docno;
    $diario->save(); 
    
    // Registra en Detallepago para generar un renglon en el recibo
    Self::registraDetallepago($periodo, '', 'Paga alquiler de '.$evento->title, '', $evento->pc_monto, $evento->un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);  

  }  


  /** 
  *=============================================================================================
  *  Esta function registra en libros devolucion de deposito por reserva de Area social & BB
  * @param  collection devolucion       
  * @return periodo
  * @return void
  *===========================================================================================*/
  public static function contabilizaDevolucionDeposito($devolucion, $periodo) {
  
    //dd($devolucion, $periodo);
    
    // encuentra los detalles del calendarevento
    $evento = Calendarevento::find($devolucion->calendarevento_id);
    
    // incializa variables a utilizar
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
    $cuenta_39 = Catalogo::find(39)->nombre;  // 2040.00 Depositos por reservacion de Area social & BB
    $cuenta_8  = Catalogo::find(8)->nombre;   // 1020.00 Banco Nacional

    // verifica si existe registro para informe de diario de caja para el dia de hoy,
    // si no existe entonces lo crea.      
    $diariocaja= Diariocaja::where('fecha', $devolucion->fecha)->first();

    if (!$diariocaja) {
      $dto = new Diariocaja; 
      $dto->fecha = $devolucion->fecha;         
      $dto->save();
    }

    // registra en ctmayores una disminucion cuenta Depositos por reservacion de Area social & BB
    Sity::registraEnCuentas(
      $periodo,
      'menos',
      2, 
      39,
      $devolucion->fecha,
      'Devolucion de reservacion de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
      $evento->res_monto,
      $evento->un_id
    );

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->fecha   = $devolucion->fecha;
    $diario->detalle = $cuenta_39.', '.$evento->un->codigo;
    $diario->debito = $evento->res_monto;
    $diario->save();  

    if ($devolucion->trantipo_id == '4' ) {  // si es banca en linea
      // registra en ctmayores un aumento en banco
      Sity::registraEnCuentas(
        $periodo,
        'menos',
        1, 
        8,
        $devolucion->fecha,
        'Devolucion de reservacion de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
        $evento->res_monto,
        $evento->un_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = $cuenta_8;
      $diario->credito = $evento->res_monto;
      $diario->save();           

    } else {  
      // registra en ctmayores un aumento en la Caja general
      Sity::registraEnCuentas(
        $periodo,
        'menos',
        1, 
        32,
        $devolucion->fecha,
        'Devolucion de reservacion de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
        $evento->res_monto,
        $evento->un_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = $cuenta_32;
      $diario->credito = $evento->res_monto;
      $diario->save();        
    } 

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id = $periodo;
    $diario->detalle = 'Para registrar devolucion por reservacion de '.$evento->title.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno;
    $diario->save(); 
    
    // Registra en Detallepago para generar un renglon en el recibo
    // Self::registraDetallepago($periodo, '', 'Paga deposito por reservacion de '.$evento->title, '', $evento->res_monto, $evento->un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);  

  }

  /** 
  *=============================================================================================
  *  Esta function registra devolucion en el pago de alquiler de Area social & BB
  * @param  collection devolucion       
  * @return periodo
  * @return void
  *===========================================================================================*/
  public static function contabilizaDevolucionAlquiler($devolucion, $periodo) {
  
    //dd($devolucion, $periodo);

    // encuentra los detalles del calendarevento
    $evento = Calendarevento::find($devolucion->calendarevento_id);

    // incializa variables a utilizar
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
    $cuenta_45 = Catalogo::find(45)->nombre;  // 4161.00 Ingresos por alquiler de Area social & BB
    $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional

    // verifica si existe registro para informe de diario de caja para el dia de hoy,
    // si no existe entonces lo crea.      
    $diariocaja= Diariocaja::where('fecha', $devolucion->fecha)->first();

    if (!$diariocaja) {
      $dto = new Diariocaja; 
      $dto->fecha = $devolucion->fecha;         
      $dto->save();
    }
    
    // registra en ctmayores una disminucion en la cuenta 4161.00 Ingresos por alquiler de Area social & BB
    Sity::registraEnCuentas(
      $periodo,
      'menos',
      4, 
      45,
      $devolucion->fecha,
      'Devolucion de alquiler de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
      $evento->pc_monto,
      $evento->un_id
    );

    //dd($evento->un->codigo);

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo;
    $diario->fecha   = $devolucion->fecha;
    $diario->detalle = $cuenta_45.', '.$evento->un->codigo;
    $diario->debito = $evento->pc_monto;
    $diario->save();  

    if ($devolucion->tipopago == '4' ) {  // sea banca en linea
      // registra en ctmayores un aumento en banco
      Sity::registraEnCuentas(
        $periodo,
        'menos',
        1, 
        8,
        $devolucion->fecha,
        'Devolucion de alquiler de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
        $evento->pc_monto,
        $evento->un_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $devolucion->fecha;
      $diario->detalle = $cuenta_8;
      $diario->credito = $evento->pc_monto;
      $diario->save();  

    } else {  
      // registra en ctmayores un aumento en la Caja general
      Sity::registraEnCuentas(
        $periodo,
        'menos',
        1, 
        32,
        $devolucion->fecha,
        'Devolucion de alquiler de '.$evento->title.', '.$evento->un->codigo.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno,
        $evento->pc_monto,
        $evento->un_id
      );

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = $cuenta_32;
      $diario->credito = $evento->pc_monto;
      $diario->save();         
    }      

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id = $periodo;
    $diario->detalle = 'Para registra devolucion por alquiler de '.$evento->title.', '.$devolucion->trantipo->siglas.', doc #'.$devolucion->docno;
    $diario->save(); 
    
    // Registra en Detallepago para generar un renglon en el recibo
    // Self::registraDetallepago($periodo, '', 'Paga deposito por reservacion de '.$evento->title, '', $evento->res_monto, $evento->un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);  

  }

} //fin de Class Npago