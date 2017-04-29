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

class Npago {
  
  /** 
  *=============================================================================================
  * Esta function comienza el proceso de contabilizar los pagos recibidos y notifica al propietario
  * @param  string        $un_id          "1"
  * @param  decimal       $montoRecibido  100.25
  * @param  integer       $pago_id        14
  * @param  string        $f_pago         "2016-01-30"
  * @param  integer       $periodo        3
  * @param  string        $pdo            "Mar-2016" 
  * @param  string        $tipoPago       "5" 
  * @return void
  *===========================================================================================*/
  public static function iniciaPago($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $pdo, $tipoPago=Null) {
    //dd($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $pdo, $tipoPago);

    // procesa el pago recibido
    self::procesaPago($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $tipoPago);
    
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
  * @param  string  $un_id         "1"
  * @param  decimal $montoRecibido 100.25 
  * @param  integer $pago_id       15    
  * @param  string  $f_pago        "2016-01-30"  
  * @param  integer $periodo       3 
  * @param  string  $tipoPago      "1"   
  * @return void
  *===========================================================================================*/
  
  public static function procesaPago($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $tipoPago) {
    //dd($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $tipoPago);

    // trae los datos de las unidades almacenados en cache y encuentra el codigo de la unidad
    $datos = Cache::get('unsAllkey');
    $unCodigo = $datos->where('id', $un_id)->first()->codigo;
    //dd($unCodigo);

    //Prioridad no 1, verifica si hay cuotas regulares pendiente por pagar.
    $datos = self::cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo);
    $sobrante = $datos['sobrante'];
    $dineroFresco = $datos['dineroFresco'];
    //dd($sobrante, $dineroFresco);    
    
    //Prioridad no 2, verifica si hay recargos pendiente por pagar.
    $datos = self::cobraRecargos($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago, $unCodigo);
    $sobrante = $datos['sobrante'];
    $dineroFresco = $datos['dineroFresco'];
    //dd($sobrante, $dineroFresco);
    
    //Prioridad no 3, verifica si hay cuotas extraordinarias por pagar.
    $datos = self::cobraCuotaExtraordinaria($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago, $unCodigo);
    $sobrante = $datos['sobrante'];
    $dineroFresco = $datos['dineroFresco'];
    //dd($sobrante, $dineroFresco);
    
    //Prioridad no 4, verifica si se trata de un pago anticipado con el proposito de obtener descuento
    $datos = Desc::verificaDescuento($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    $sobrante = $datos['sobrante'];
    $dineroFresco = $datos['dineroFresco'];
    //dd($sobrante, $dineroFresco);

    //Prioridad no 5, si al final de todo el proceso hay un sobrante entonces se registra como Pago anticipado
    // si sobra dinero y es fresco se tiene que registrar en el banco o caja chica segun el tipo de pago que se hizo
    // si sobra dinero pero no es fresco no se tiene que hacer nada ya que ese dinero ya esta contabilizado en la cuenta de pagos anticipados
    //dd($sobrante, $dineroFresco); 

    if ($sobrante > 0 && $dineroFresco) {
       $sobrante = self::registraSobranteFinal($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago, $unCodigo);
    }
  }
 

  /** 
  *=========================================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
  * @param  integer     $periodo        3
  * @param  string      $un_id          "1"
  * @param  float       $montoRecibido  100.25
  * @param  integer     $pago_id        16
  * @param  string      $f_pago         "2016-01-30"  
  * @param  string      $tipoPago       Null, "1"  
  * @param  string      $tipoPago      "1A-T100R2"  
  * @return void
  *=========================================================================================================*/
  public static function cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo);

    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
            ->where('un_id', $un_id)
            ->where('pagada', 0)
            ->orderBy('fecha', 'asc')
            ->get();
    //dd($datos->toArray());

    // incializa variables a utilizar
    if ($montoRecibido > 0) {
      $dineroFresco = true;
    } else {
      $dineroFresco = false;
    }
  
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
      $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);
      
      foreach ($datos as $dato) {
        $importe = round((float)$dato->importe, 2);         
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;

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

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco o caja general
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento regulares por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_1.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $importe, $un_id, $pago_id);   
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por "Cuotas de mantenimiento regulares por cobrar"
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_1.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $importe, $un_id, $pago_id);     
            }
            
            // registra en el diario
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1.', '.$dato->mes_anio;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$mesAnio, $importe, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $importe), 2);
            
            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }
            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $importe), 2);

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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, $importe, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$mesAnio, $importe, $un_id, $pago_id);

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
            $dineroFresco = false;
            $hayPie = true;
          
          } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - ($importe - $montoRecibido)), 2);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento regulares por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_1.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id); 
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_1.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id);   
            }
             
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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, ($importe - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento regulares por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$mesAnio, $importe, $un_id, $pago_id);

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
            $dineroFresco = false;
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
        $diario->detalle = 'Para registrar cobro de couta de mantenimiento regular, unidad '.$unCodigo.', Pago #'.$pago_id;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 
    
    } // endif #1
  
  } // end function
 
  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todos los recargos posibles dependiendo del monto disponible.
  * @param  integer       $periodo        1
  * @param  string        $un_id          "7"
  * @param  decimal       $montoRecibido  0.5
  * @param  integer       $pago_id        10
  * @param  string        $f_pago         "2016-01-30"          
  * @param  string        $tipoPago      "1A-T100R2"  
  * @return void
  *===========================================================================================*/
  public static function cobraRecargos($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo);

    // Encuentra todos los recargos por pagar
    $datos = Ctdasm::where('pcontable_id','<=', $periodo)
             ->where('un_id', $un_id)
             ->whereDate('f_vencimiento','<', $f_pago)
             ->where('recargo_siono', 1)
             ->where('recargo_pagado', 0)
             ->get();
    //dd($datos->toArray());

    // incializa variables a utilizar
    if ($montoRecibido > 0) {
      $dineroFresco = true;
    
    } else {
      $dineroFresco = false;
    }
  
    $regresa = array();

    // si hay recargos en cuotas de mantenimiento regulares por cobrar continua proceso, si no hay regresa
    // if #1
    if (!$datos->isEmpty()) { 
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa = Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);  
      // dd($saldocpa);

      // incializa variables a utilizar
      $cuenta_2 = Catalogo::find(2)->nombre;      // 1130.00 Recargo en cuota de mantenimiento por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);
      
      foreach ($datos as $dato) {
        $recargo = round((float)$dato->recargo, 2); 
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;

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

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco o caja general
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $recargo;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Recargo en cuota de mantenimiento por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_2.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $recargo, $un_id, $pago_id);   
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $recargo;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por "Recargo en cuota de mantenimiento por cobrar"
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_2.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $recargo, $un_id, $pago_id);     
            }
            
            // registra en el diario
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2.', '.$mesAnio;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$mesAnio, $recargo, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento regular '. $mesAnio, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $recargo), 2);

            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }
            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $recargo), 2);

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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, $recargo, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$mesAnio, $recargo, $un_id, $pago_id);

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
            $dineroFresco = false;
            $hayPie = true;

          } elseif ($montoRecibido < $recargo) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - ($recargo - $montoRecibido)), 2);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Recargo en cuota de mantenimiento por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_2.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id); 
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_2.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id);   
            }
             
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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, ($recargo - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$mesAnio, $recargo, $un_id, $pago_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular de '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

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
            $dineroFresco = false;
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
        $diario->detalle = 'Para registrar cobro de recargo en couta de mantenimiento regular, unidad '.$unCodigo.', Pago #'.$pago_id;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 
    
    } // endif #1
  
  } // end function

  /** 
  *==================================================================================================================
  * Este proceso se encarga de cobrar todas las cuotas extraordinarias posibles dependiendo del monto disponible.
  * @param  integer   $periodo        3
  * @param  string    $un_id          "1"
  * @param  decimal   $montoRecibido  100.25
  * @param  integer   $pago_id        16
  * @param  string    $f_pago         "2016-01-30"  
  * @param  string    $tipoPago       "1A-T100R2"  
  * @return void
  *==================================================================================================================*/
  public static function cobraCuotaExtraordinaria($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago, $unCodigo) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $unCodigo);

    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
          ->where('un_id', $un_id)
          ->where('extra_siono', 1)
          ->where('extra_pagada', 0)
          ->orderBy('fecha', 'asc')
          ->get();
    //dd($datos->toArray());

    // incializa variables a utilizar
    if ($montoRecibido > 0) {
      $dineroFresco = true;
    } else {
      $dineroFresco = false;
    }
  
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
      $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
      $i = 0;
      $hayPie = false;

      // me aseguro de que todas las variables involucradas sean de tipo float y redondeadas a dos decimales
      $montoRecibido = round((float)$montoRecibido, 2);
      $saldocpa = round((float)$saldocpa, 2);

      foreach ($datos as $dato) {
        $extra = round((float)$dato->extra, 2); 
        $ocobro = $dato->ocobro;
        $mesAnio = $dato->mes_anio;

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

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco o caja general
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $extra;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento extraordinarias por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_16.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $extra, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $extra;
              $diario->credito = Null;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por "Cuotas de mantenimiento extraordinarias por cobrar"
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_16.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $extra, $un_id, $pago_id);    
            }
            
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
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$mesAnio, $extra, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $extra, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $montoRecibido = round(($montoRecibido - $extra), 2);

            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }
            $hayPie = true;
          
          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            // redondeo el resultado para eliminar decimales extras producto de la resta
            $saldocpa = round(($saldocpa - $extra), 2);

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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, $extra, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$mesAnio, $extra, $un_id, $pago_id);

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
            $dineroFresco = false;
            $hayPie = true;

          } elseif ($montoRecibido < $extra) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = round(($saldocpa - ($extra - $montoRecibido)), 2);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento extraordinarias por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_16.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id);  
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              if ($i == 0) { $diario->fecha = $f_pago; }
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              $i = 1;

              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_16.', '.$mesAnio.', '.$unCodigo.', Pago #'.$pago_id, $montoRecibido, $un_id, $pago_id);  
            }
             
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
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, ($extra - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$mesAnio, $extra, $un_id, $pago_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular de '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $extra, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

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
            $dineroFresco = false;
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
        $diario->detalle = 'Para registrar cobro de couta de mantenimiento extraordinaria, unidad '.$unCodigo.', Pago #'.$pago_id;
        $diario->save();
      }

      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 

    } else {
      // regresa arreglo de datos
      $regresa["sobrante"] = $montoRecibido;
      $regresa["dineroFresco"] = $dineroFresco;    

      return $regresa; 
    
    } // endif #1
  
  } // end function
  
  /** 
  *=============================================================================================
  * Registra el sobrante final si existe
  * @param  integer     $periodo        1
  * @param  string      $un_id          "1"
  * @param  decimal     $montoRecibido  100.25
  * @param  integer     $pago_id        6
  * @param  string      $f_pago         "2016-01-30"  
  * @param  string      $tipoPago       Null, "1"  
  * @return void
  *===========================================================================================*/
  
  public static function registraSobranteFinal($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago, $unCodigo) {
    //dd($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    
    // incializa variables a utilizar
    $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
    $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
    $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general

    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //dd($saldocpa);

    // hace los asientos contables dependiendo del tipo de pago
    // 2= Transferencia 3= ACH  4= Banca en linea
    // se afecta derectamente a la cuenta de banco o caja general
    if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

      // registra en el diario
      // registra un aumento en la cuenta Banco por "Anticipos recibidos de propietarios"
      $diario = new Ctdiario;
      $diario->pcontable_id = $periodo;
      $diario->fecha   = $f_pago; 
      $diario->detalle = $cuenta_8;
      $diario->debito  = $sobrante;
      $diario->credito = Null;
      $diario->save();
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = $cuenta_5;
      $diario->debito = Null;
      $diario->credito = $sobrante;
      $diario->save();

      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = 'Para registrar pago anticipado, unidad '.$unCodigo.', Pago #'.$pago_id;
      $diario->save();

      // registra en el mayor
      // registra un aumento en la cuenta Banco por "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_5.', '.$unCodigo.', Pago #'.$pago_id, $sobrante, $un_id, $pago_id);    
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, $sobrante, $un_id, $pago_id); 
    
    } else {

      // registra en el diario
      // registra un aumento en la cuenta de Caja general 
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->fecha   = $f_pago; 
      $diario->detalle = $cuenta_32;
      $diario->debito  = $sobrante;
      $diario->credito = Null;
      $diario->save();
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = $cuenta_5;
      $diario->debito = Null;
      $diario->credito = $sobrante;
      $diario->save();

      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = 'Para registrar pago anticipado, unidad '.$unCodigo.', Pago #'.$pago_id;
      $diario->save();

      // registra en el mayor
      // registra un aumento en la cuenta de Caja general 
      Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_5.', '.$unCodigo.', Pago #'.$pago_id, $sobrante, $un_id, $pago_id);    
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, $cuenta_5.', Pago #'.$pago_id, $sobrante, $un_id, $pago_id); 
    }

    // salva un nuevo registro que representa una linea del recibo
    $dto = new Detallepago;
    $dto->pcontable_id = $periodo;
    $dto->detalle = 'Estimado propietario, producto de contabilizar el pago realizado contra la totalidad adeudada a la fecha, el sistema ha detectado que existe un sobrante de B/. '.number_format($sobrante,2). ', por lo tanto el mismo será depositado en su cuenta de pagos anticipados. Este saldo usted lo podrá utilizar para completar futuros pagos. El nuevo saldo de su cuenta de pagos anticipados a la fecha es de B/.'.number_format(($saldocpa+$sobrante),2);
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
    $pago=Pago::find($pago_id);
    $pago->anulado  = 1;
    $pago->save(); 

    $f_pago= Carbon::parse($pago->f_pago);
    
    // determina cuantos periodos contables fueron afectados por el presente pago
    $periodos= Ctmayore::where('pago_id', $pago_id)
                ->select('pcontable_id')
                ->get();
    
    $periodos= $periodos->unique('pcontable_id');    
    //dd($detalles->toArray()); 

    foreach ($periodos as $periodo) {
      $debitos= Ctmayore::where('pago_id', $pago_id)
                  ->where('pcontable_id', $periodo->pcontable_id)
                  ->where('debito', 0)
                  ->get();
      // dd($detalles->toArray());
      
      $creditos= Ctmayore::where('pago_id', $pago_id)
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
        if ($i==1) {
          $diario->fecha  = Carbon::today();
        }       
        $diario->detalle = $debito->detalle;
        $diario->debito = $debito->credito;
        $diario->save();
        $i=0;
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
        if ($i==1) {
          $diario->fecha  = Carbon::today();
        }  
        $diario->detalle = $credito->detalle;
        $diario->credito = $credito->debito;
        $diario->save();
      }

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo->pcontable_id;
      $diario->detalle = 'Para anular Pago No. '.$pago_id;
      $diario->save();       
    }
      
    // tercero, si con el pago se logro pagar una o mas cuotas de mantenimiento,
    // entonces se procede a revertir en la tabla ctdasms
    $datos= Ctmayore::where('pago_id', $pago_id)
                ->where('cuenta', 1)
                ->select('ctdasm_id')
                ->get();
    
    $datos= $datos->unique('ctdasm_id');    
    //dd($detalles->toArray());

    foreach ($datos as $dato) {
      Ctdasm::where('id', $dato->ctdasm_id)
            ->update(['pagada' => 0]);
    }

    // cuarto, si con el pago se logro pagar uno  o mas recargos en cuotas de mantenimiento,
    // entonces se procede a revertir en la tabla ctdasms
    $datos= Ctmayore::where('pago_id', $pago_id)
                ->where('cuenta', 2)
                ->select('ctdasm_id')
                ->get();
    
    $datos= $datos->unique('ctdasm_id');    
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

    $i= 1;   
    
    // inicializa variable para almacenar el total de recargos
    $totalRecargos= 0;       

    if ($datos->count()) {
      foreach ($datos as $dato) {
        $dto = Ctdasm::find($dato->id);
        $dto->recargo_siono= 1;
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
              'Recargo en cuota de mantenimiento por cobrar unidad '.$dato->ocobro,
              $dato->recargo,
              $dato->un_id
             );

        // registra 'Ingreso por cuota de mantenimiento' 4130.00
        Sity::registraEnCuentas(
              $periodo_id,
              'mas',
              4,
              4, //'4130.00'
              Carbon::parse($dato->f_vencimiento)->addDay(),
              '   Ingreso por recargo en cuota de mantenimiento unidad '.$dato->ocobro,
              $dato->recargo,
              $dato->un_id
             );

        // registra resumen de la facturacion mensual en Ctdiario principal 
        if ($i==1) {
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
      $dto->detalle = 'Para registrar resumen de recargos en cuotas de mantenimiento por cobrar vencidas a '.Date::parse($dato->f_vencimiento)->toFormattedDateString();
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


} //fin de Class Npago