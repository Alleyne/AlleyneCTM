<?php namespace App\library;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use App\library\Sity;
use App\library\Pant;
use App\Notifications\emailNuevaOcobro;
use Session, DB;

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
    
    $props= Prop::where('un_id', $un_id)
           ->where('encargado', 1)
           ->join('users','users.id','=','props.user_id')
           ->select('users.id','email','nombre_completo')
           ->get();
    //dd($props->toArray());
    
    // envia email a cada uno de los propietarios de la unidad que sean encargados  
    /* foreach ($props as $prop) {
        Mail::to($prop->email, $prop->nombre_completo)
            ->queue(new sendnuevoEcuentas($datos['data'], $datos['imps'], $datos['recs']));
       }*/

    // envia una notificacion via email a cada uno de los propietarios de la unidad que sean encargados  
    foreach ($props as $prop) {
      $user= User::find($prop->id);
      //dd($user);
      $user->notify(new emailNuevaOcobro($pdo, $user->nombre_completo));      
    } 
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
    dd($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $tipoPago);

    //Prioridad no 1, verifica si hay cuotas regulares pendiente por pagar.
    $datos = self::cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago);
    $sobrante = $datos['sobrante'];
    $dineroFresco = $datos['dineroFresco'];
    dd($sobrante, $dineroFresco);    
    
    //Prioridad no 2, verifica si hay recargos pendiente por pagar.
    $sobrante = self::cobraRecargos($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    //dd($sobrante);
    
    //Prioridad no 3, verifica si hay cuotas extraordinarias por pagar.
    $sobrante = self::cobraCuotaExtraordinaria($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    //dd($sobrante);
    
    //Prioridad no 4, verifica si se trata de un pago anticipado con el proposito de obtener descuento
    $sobrante= Desc::verificaDescuento($periodo, $un_id, $sobrante, $pago_id, $f_pago);
    //dd($sobrante);

    //Prioridad no 5, si al final de todo el proceso hay un sobrante entonces se registra como Pago anticipado
    
    // si sobra dinero y es fresco se tiene que registrar en el banco o caja chica segun el tipo de pago que se hizo
    // si sobra dinero pero no es fresco no se tiene que hacer nada ya que ese dinero ya esta contabilizado en la cuenta de pagos anticipados
    //dd($sobrante, $dineroFresco); 

    if ($sobrante > 0 && $dineroFresco) {
       $sobrante = self::registraSobranteFinal($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    }
  }
 
  /** 
  *=========================================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
  * @param  integer     $periodo        3
  * @param  string      $un_id          "1"
  * @param  decimal     $montoRecibido  100.25
  * @param  integer     $pago_id        16
  * @param  string      $f_pago         "2016-01-30"  
  * @param  string      $tipoPago       Null, "1"  
  * @return void
  *=========================================================================================================*/
  public static function cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago);
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);
      // dd($saldocpa);

      // incializa variables a utilizar
      $dineroFresco = true;
      $cuenta_1 = Catalogo::find(1)->nombre;    // 1120.00 Cuota de mantenimiento regular por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general
      $montoRecibido= round(floatval($montoRecibido),2);  
      
      foreach ($datos as $dato) {
        $importe= round(floatval($dato->importe),2);               
        $ocobro= $dato->ocobro;
     
        if (($montoRecibido + $saldocpa) >= $importe) {
          // hay suficiente dinero para pagar por lo menos una cuota de  mantenimiento
          // por lo tanto, registra la cuota mensual como pagada
          $dato->pagada= 1;
          $dato->save();            

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
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuota de mantenimiento regular por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_1.', '.$ocobro, $importe, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por "Cuota de mantenimiento regular por cobrar"
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_1.', '.$ocobro, $importe, $un_id, $pago_id);    
            }
            
            // registra en el diario
            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();
          
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$ocobro, $importe, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = $montoRecibido - $importe;

            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $importe;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', '.$ocobro, $importe, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$ocobro, $importe, $un_id, $pago_id);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota de mantenimiento de '.$ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
            } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = $saldocpa - ($importe - $montoRecibido);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuota de mantenimiento regular por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_1.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_1.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            }
           
            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->fecha   = $f_pago; 
            $diario->detalle = $cuenta_5;
            $diario->debito = ($importe - $montoRecibido);
            $diario->credito = Null;
            $diario->save();
          
            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_1;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $cuenta_5.', '.$ocobro, ($importe - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, $cuenta_1.', '.$ocobro, $importe, $un_id, $pago_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($importe - $montoRecibido,2). ' para completar pago de cuota de mantenimiento de '.$ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
          } else {
            return '<< ERROR >> en function cobraFacturas()';
          }
        } 
      }   // end foreach      

      // regresa arreglo de datos
      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = $dineroFresco;    

      return $datos; 
    
    } else {
      // regresa arreglo de datos
      if ($montoRecibido == Null) {
        $montoRecibido = 0;
      }

      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = true;    
      
      return $datos; 
    }
  }  // end function

  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todos los recargos posibles dependiendo del monto disponible.
  * @param  integer       $periodo        1
  * @param  string        $un_id          "7"
  * @param  decimal       $montoRecibido  0.5
  * @param  integer       $pago_id        10
  * @param  string        $f_pago         "2016-01-30"          
  * @return void
  *===========================================================================================*/
  public static function cobraRecargos($periodo, $un_id, $montoRecibido, $pago_id, $f_pago) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    
    // Encuentra todos los recargos por pagar
    $datos = Ctdasm::where('pcontable_id','<=', $periodo)
                   ->where('un_id', $un_id)
                   ->whereDate('f_vencimiento','<', $f_pago)
                   ->where('recargo_siono', 1)
                   ->where('recargo_pagado', 0)
                   ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);
      // dd($saldocpa);

      // incializa variables a utilizar
      $dineroFresco = true;
      $cuenta_2 = Catalogo::find(2)->nombre;    //2 1130.00 Recargo en cuota de mantenimiento por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    //5 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 = Catalogo::find(8)->nombre;    //8 1020.00 Banco
      $cuenta_32 = Catalogo::find(32)->nombre;  //32 1000.00 Caja general
      $montoRecibido= round(floatval($montoRecibido),2);
      
      foreach ($datos as $dato) {
        $recargo= round(floatval($dato->recargo),2);
        $ocobro= $dato->ocobro;
        
        if (($montoRecibido + $saldocpa) >= $recargo) {
          // hay suficiente dinero para pagar por lo menos un recargo en cuota de  mantenimiento
          // por lo tanto, registra el recargo como pagado
          $dato->recargo_pagado = 1;
          $dato->save();            

          if ($montoRecibido >= $recargo) {
            // se recibio suficiente dinero para pagar por lo menos un recargo,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco o caja general
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta 8 1020.00 Banco 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $recargo;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_2.', '.$ocobro, $recargo, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta 32 1000.00 Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $recargo;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_2.', '.$ocobro, $recargo, $un_id, $pago_id);    
            }
            
            // registra en el diario
            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();
          
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de recargo en couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar  
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$ocobro, $recargo, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento regular, '. $ocobro, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = $montoRecibido - $recargo;

            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $recargo;

            // registra en el diario
            // registra un disminucion en la cuenta 5 2010.00 Anticipos recibidos de propietarios 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $recargo;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de recargo en couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;

            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 5 2010.00 Anticipos recibidos de propietarios
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', '.$ocobro, $recargo, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$ocobro, $recargo, $un_id, $pago_id);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($recargo),2). ' para completar pago de recargo en cuota de mantenimiento de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $recargo;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
          } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = $saldocpa - ($recargo - $montoRecibido);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta 8 1020.00 Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_2.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta 32 1000.00 Caja general
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_2.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            }
           
            // registra en el diario
            // registra un disminucion en la cuenta 5 2010.00 Anticipos recibidos de propietarios  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->fecha   = $f_pago; 
            $diario->detalle = $cuenta_5;
            $diario->debito = ($recargo - $montoRecibido);
            $diario->credito = Null;
            $diario->save();
          
            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_2;
            $diario->debito = Null;
            $diario->credito = $recargo;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de recargo en couta de mantenimiento regular, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $cuenta_5.', '.$ocobro, ($recargo - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, $cuenta_2.', '.$ocobro, $recargo, $un_id, $pago_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento regular, '. $ocobro, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($recargo - $montoRecibido,2). ' para completar pago de recargo en cuota de mantenimiento de '.$ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
          } else {
            return '<< ERROR >> en function cobraFacturas()';
          }
        } 
      }   // end foreach      

      // regresa arreglo de datos
      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = $dineroFresco;    
      
      return $datos; 
    
    } else {
      // regresa arreglo de datos
      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = true;    
      
      return $datos; 
    }
  }  // end function

/** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todas las cuotas extraordinarias posibles dependiendo del monto disponible.
  * @param  integer     $periodo        3
  * @param  string      $un_id          "1"
  * @param  decimal     $montoRecibido  100.25
  * @param  integer     $pago_id        16
  * @param  string      $f_pago         "2016-01-30"  
  * @return void
  *===========================================================================================*/
  public static function cobraCuotaExtraordinaria($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago, $tipoPago);
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('extra_siono', 1)
                  ->where('extra_pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);
      // dd($saldocpa);

      // incializa variables a utilizar
      $dineroFresco = true;
      $cuenta_16 =  Catalogo::find(16)->nombre;  // 1110.00 Cuotas de mantenimiento extraordinarias por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;     // 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 = Catalogo::find(8)->nombre;     // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;   // 1000.00 Caja general
      $montoRecibido= round(floatval($montoRecibido),2);

      foreach ($datos as $dato) {
        $importe= round(floatval($dato->importe),2);  
        $ocobro= $dato->ocobro;

        if (($montoRecibido + $saldocpa) >= $importe) {
          // hay suficiente dinero para pagar por lo menos una cuota extraordinaria
          // por lo tanto, registra la cuota extraordinaria como pagada
          $dto = ctdasm::find($dato->id);
          $dto->extra_pagada= 1;
          $dto->save();  

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
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento extraordinarias por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_16.', '.$ocobro, $importe, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $importe;
              $diario->credito = Null;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general por "Cuotas de mantenimiento extraordinarias por cobrar"
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_16.', '.$ocobro, $importe, $un_id, $pago_id);    
            }
            
            // registra en el diario
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();
          
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento extraordinaria, unidad '.$ocobro.', Pago #'.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar"  
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$ocobro, $importe, $un_id, $pago_id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = $montoRecibido - $importe;

            // si hay dinero sobrante quiere decir que este dinero es fresco no proviene de cuentas de pagos anticipados
            if ($montoRecibido > 0) {
              $dineroFresco = true;
            }

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_5;
            $diario->debito = $importe;
            $diario->credito = Null;
            $diario->save();

            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento extraordinaria, unidad '.$ocobro.', Pago no. '.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, $cuenta_5.', '.$ocobro, $importe, $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$ocobro, $importe, $un_id, $pago_id);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota de mantenimiento extraordinaria de '.$ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
            } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = $saldocpa - ($importe - $montoRecibido);

            // hace los asientos contables dependiendo del tipo de pago
            // 2= Transferencia 3= ACH  4= Banca en linea
            // se afecta derectamente a la cuenta de banco
            if ($tipoPago == 2 || $tipoPago == 3 || $tipoPago == 4) {

              // registra en el diario
              // registra un aumento en la cuenta Banco  
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_8;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta Banco por "Cuotas de mantenimiento extraordinarias por cobrar" 
              Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_16.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            
            } else {
              
              // registra en el diario
              // registra un aumento en la cuenta de Caja general 
              $diario = new Ctdiario;
              $diario->pcontable_id  = $periodo;
              $diario->fecha   = $f_pago; 
              $diario->detalle = $cuenta_32;
              $diario->debito  = $montoRecibido;
              $diario->save();
              
              // registra en el mayor
              // registra un aumento en la cuenta de Caja general 
              Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_16.', '.$ocobro, $montoRecibido, $un_id, $pago_id);    
            }
           
            // registra en el diario
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->fecha   = $f_pago; 
            $diario->detalle = $cuenta_5;
            $diario->debito = ($importe - $montoRecibido);
            $diario->credito = Null;
            $diario->save();
          
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = $cuenta_16;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento extraordinaria, unidad '.$ocobro.', Pago no. '.$pago_id;
            $diario->save();

            // registra en el mayor
            // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $cuenta_5.', '.$ocobro, ($importe - $montoRecibido), $un_id, $pago_id);

            // registra en el mayor
            // registra un disminucion en la cuenta 1120.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, $cuenta_16.', '.$ocobro, $importe, $un_id, $pago_id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento extraordinaria de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($importe - $montoRecibido,2). ' para completar pago de cuota de mantenimiento extraordinaria '.$ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    
            $dineroFresco = false;
          
          } else {
            return '<< ERROR >> en function cobraCuotaExtraordinaria()';
          }
        } 
      }   // end foreach      

      // regresa arreglo de datos
      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = $dineroFresco;    

      return $datos; 
    
    } else {
      // regresa arreglo de datos
      if ($montoRecibido == Null) {
        $montoRecibido = 0;
      }

      $datos["sobrante"] = $montoRecibido;
      $datos["dineroFresco"] = true;    
      
      return $datos; 
    }
  }  // end function

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
  
  public static function registraSobranteFinal($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago) {
    //dd($periodo, $un_id, $sobrante, $pago_id, $f_pago, $tipoPago);
    
    // encuentra el codigo de la unidad
    $unCodigo= Un::find($un_id)->codigo;
    //dd($unCodigo);
   
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
      $diario->pcontable_id  = $periodo;
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
      $diario->detalle = 'Para registrar pago anticipado, unidad '.$unCodigo.', Pago no. '.$pago_id;
      $diario->save();

      // registra en el mayor
      // registra un aumento en la cuenta Banco por "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, $cuenta_5.', '.$unCodigo, $sobrante, $un_id, $pago_id);    
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, $cuenta_5.', '.$unCodigo, $sobrante, $un_id, $pago_id); 
    
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
      $diario->detalle = 'Para registrar pago anticipado, unidad '.$unCodigo.', Pago no. '.$pago_id;
      $diario->save();

      // registra en el mayor
      // registra un aumento en la cuenta de Caja general 
      Sity::registraEnCuentas($periodo, 'mas', 1, 32, $f_pago, $cuenta_5.', '.$unCodigo, $sobrante, $un_id, $pago_id);    
      
      // registra un aumento en la cuenta 2010.00 "Anticipos recibidos de propietarios"
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, $cuenta_5.', '.$unCodigo, $sobrante, $un_id, $pago_id); 
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
        $ocobro= $dato->ocobro;
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
              'Recargo en cuota de mantenimiento por cobrar unidad '.$ocobro,
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
              '   Ingreso por recargo en cuota de mantenimiento unidad '.$ocobro,
              $dato->recargo,
              $dato->un_id
             );

        // registra resumen de la facturacion mensual en Ctdiario principal 
        if ($i==1) {
          // registra en Ctdiario principal
          $dto = new Ctdiario;
          $dto->pcontable_id = $periodo_id;
          $dto->fecha   = Carbon::parse($dato->f_vencimiento)->addDay();
          $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$ocobro;
          $dto->debito  = $dato->recargo; 
          $dto->save(); 
        
        } else {
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id  = $periodo_id;
            $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$ocobro;
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