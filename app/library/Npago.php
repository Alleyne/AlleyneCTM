<?php namespace App\library;

use Jenssegers\Date\Date;
use App\library\Sity;
use App\library\Pant;
use App\Notifications\emailNuevaOcobro;

use App\Un;
use App\Prop;
use App\User;
use App\Ctdasm;
use App\Catalogo;
use App\Ctdiario;
use App\Detallepago;
use App\Pcontable;
use App\Ctmayore;

class Npago {

  /** 
  *=============================================================================================
  * Esta function comienza el proceso de contabilizar los pagos recibidos
  * @param  string  $un_id          "1"
  * @param  decimal $montoRecibido  100.25
  * @param  integer $pago_id        14
  * @param  string  $f_pago         "2016/03/03" 
  * @param  integer $periodo        3
  * @param  string  $pdo            "Mar-2016" 
  * @return void
  *===========================================================================================*/
  public static function iniciaPago($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $pdo) {
    //dd($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $pdo);

    // procesa el pago recibido
    self::procesaPago($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
 
    // llama al proceso que pasa los registro del pago del ctmayores al ctdiarios 
    if ($pago_id && $montoRecibido) {
      // si $pago_id y $montoRecibido existen, quiere decir que se trata de un pago normal      
      self::registaEnDiario($pago_id, $periodo);
    } 
 
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
  * @param  integer $periodo       3 
  * @param  string  $un_id         "1"
  * @param  decimal $montoRecibido 100.25 
  * @param  integer $pago_id       15      
  * @param  string  $f_pago        "2016/03/03"  
  * @return void
  *===========================================================================================*/
  public static function procesaPago($periodo, $un_id, $montoRecibido, $pago_id, $f_pago)
  {
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);

    //Prioridad no 1, verifica si hay cuotas regulares pendiente por pagar.
    $sobrante = self::cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    //dd($sobrante);
    
    //Prioridad no 2, verifica si hay recargos pendiente por pagar.
    $sobrante = self::cobraRecargos($periodo, $un_id, $sobrante, $pago_id, $f_pago);

    //Prioridad no 3, verifica si hay cuotas extraordinarias por pagar.
    $sobrante = self::cobraCuotaExtraordinaria($periodo, $un_id, $sobrante, $pago_id, $f_pago);
    
    //Prioridad no 4, verifica si se trata de un pago anticipado con el proposito de obtener descuento
    $sobrante= Desc::verificaDescuento($un_id, $sobrante, $pago_id, $periodo, $f_pago);

    // si al final de todo el proceso hay un sobrante entonces se registra como Pago anticipado
    if ($sobrante > 0) {
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      //dd($saldocpa);

      // registra pago recibido como un pago anticipado
      Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.Un::find($un_id)->codigo, $sobrante, $un_id, $pago_id);
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.Un::find($un_id)->codigo, $sobrante, $un_id, $pago_id);

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
  }
 
  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
  * @param  integer $periodo        3
  * @param  string  $un_id          "1"
  * @param  decimal $montoRecibido  100.25
  * @param  integer $pago_id        16
  * @param  string  $f_pago         "2016/03/03"  
  * @return void
  *===========================================================================================*/
  public static function cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //$saldocpa= round(floatval($saldocpa),2);    
    // dd($saldocpa);

    //$montoRecibido= round(floatval($montoRecibido),2);
    
    if ($datos) {
      foreach ($datos as $dato) {
        $importe= round(floatval($dato->importe),2);               
        
        if (($montoRecibido + $saldocpa) >= $importe) {
          // hay suficiente dinero para pagar por lo menos una cuota de  mantenimiento
          // por lo tanto, registra la cuota mensual como pagada
          $dato->pagada= 1;
          $dato->save();            

          if ($montoRecibido >= $importe) {
            // se recibio suficiente dinero para pagar por lo menos una cuota,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $importe, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota de mantenimiento de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido= $montoRecibido- $importe;

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), $importe, $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'mas', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);
            //dd($saldocpa, $sobrante);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota de mantenimiento de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } elseif ($montoRecibido == Null && $pago_id == Null) {
            // si $montoRecibido y $pago_id son nulos, quiere decir que el sistema acaba de crear un nuevo periodo contable,
            // ejecuto la facturacion y esta tratando de utilizar la cuenta de pagos adelantados para cubrir por lo menos una cuota
            // de mantenimiento o recargo. En ese caso se emite una nota al propietario donde se le informa que se hizo uso de su cuenta de 
            // pagos por anticipados para cubrir la deuda, no es necesario emitir un recibo.  

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), $importe, $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);
            //dd($saldocpa, $sobrante);
            
            // registra en el diario
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->fecha   = $f_pago; 
            $diario->detalle = Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9);
            $diario->debito  = $importe;
            $diario->credito = Null;
            $diario->save();
          
            // registra en el diario
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = Catalogo::find(1)->nombre.' unidad '.$dato->ocobro;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en Ctdiario principal
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = 'Para registrar cobro de couta de mantenimiento, unidad '.$dato->ocobro;
            $diario->save();

            // se envia notificacion via email, para eso encuentra todos los propietarios encargados de la unidad
            $props= Prop::where('un_id', $un_id)->where('encargado', 1)->get();
            
            // notifica a cada uno
            foreach ($props as $prop) {
              $nota = 'Para notificarle que, se descontó  de su cuenta de pagos anticipados un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota de mantenimiento de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);

              $user= User::find($prop->user_id);              
              $user->notify(new emailUsoDeCuentaAnticipados($nota, $user->nombre_completo));
            }

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = $saldocpa-($importe - $montoRecibido);

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $montoRecibido, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$dato->ocobro, $montoRecibido, $un_id, $pago_id, Null, $dato->id);
           
            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota de mantenimiento de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), ($importe - $montoRecibido), $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$dato->ocobro, ($importe - $montoRecibido), $un_id, $pago_id, Null, $dato->id);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($importe - $montoRecibido,2). ' para completar pago de cuota de mantenimiento de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } else {
            return '<< ERROR >> en function cobraFacturas()';
          }
        } 
      }   // end foreach      
    } 
    
    //return round(floatval($montoRecibido),2);    
    return $montoRecibido; 
  }  // end function

  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todos los recargos posibles dependiendo del monto disponible.
  * @param  string $periodo
  * @param  string $un_id
  * @param  string $montoRecibido
  * @param  string $pago_id
  * @param  date   $f_pago  
  * @return void
  *===========================================================================================*/
  public static function cobraRecargos($periodo, $un_id, $montoRecibido, $pago_id, $f_pago)
  {   
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    // Encuentra todos los recargos por pagar
    $datos = Ctdasm::where('pcontable_id','<=', $periodo)
                   ->where('un_id', $un_id)
                   ->whereDate('f_vencimiento','<', $f_pago)
                   ->where('recargo_siono', 1)
                   ->where('recargo_pagado', 0)
                   ->where('pagada', 1)
                   ->get();
    //dd($datos->toArray());
    
    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //dd($saldocpa);
 
    // tiene facturacion por pagar?
    if ($datos) {
      foreach ($datos as $dato) {
        $recargo= round(floatval($dato->recargo),2);
        $ocobro= $dato->ocobro;
        
        if (($montoRecibido+ $saldocpa) >= $recargo) {
          // hay suficiente dinero para pagar por lo menos un recargo en cuota de  mantenimiento
          // por lo tanto, registra el recargo como pagado
          $dato->recargo_pagado = 1;
          $dato->save();                  
    
          if ($montoRecibido >= $recargo) {
            // se recibio suficiente dinero para pagar por lo menos un recargo,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados
            
            // registra un aumento en la cuenta 1010.00 "Cuentas por cobrar por recargo en cuotas de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $recargo, $un_id, $pago_id);

            // registra un disminucion en la cuenta 1130.00 "Cuentas por cobrar por recargo en cuota de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' unidad '.$dato->ocobro, $recargo, $un_id, $pago_id, Null, $dato->id);

            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Paga recargo en cuota de mantenimiento de '. $dato->mes_anio, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 2);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = $montoRecibido - $recargo;    

          } elseif ($montoRecibido == 0) {
            // si el monto recibido es cero, entonces 
            // se depende en su totalidad de la cuenta de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $recargo;

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), $recargo, $un_id, $pago_id);
            
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' '.$dato->ocobro, $recargo, $un_id, $pago_id);
            //dd($saldocpa, $sobrante);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($recargo,2). ' para completar el pago de recargo de '. $dato->mes_anio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $recargo;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } elseif ($montoRecibido < $recargo) {
            // si el monto recibido es menor que el recargo a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para poder realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - ($recargo-$montoRecibido);

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $montoRecibido, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' unidad  '.$dato->ocobro, $montoRecibido, $un_id, $pago_id, Null, $dato->id);
           
            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento de '. $dato->mes_anio, $dato->id, $recargo, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), ($recargo-$montoRecibido), $un_id, $pago_id);
            
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' '.$dato->ocobro, ($recargo-$montoRecibido), $un_id, $pago_id);
            
            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Se descuenta de su cuenta de pagos por anticipados', $dato->id, ($recargo-$montoRecibido), $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format(($recargo-$montoRecibido),2). ' para completar el pago de recargo de '. $dato->mes_anio.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $recargo;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } else {
            return '<< ERROR >> en function cobraFacturas()';
          }
        } 
      }   // end foreach
    } 
    return $montoRecibido;
  }  // end function

  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todas las cuotas extraordinarias posibles dependiendo del monto disponible.
  * @param  integer $periodo        3
  * @param  string  $un_id          "1"
  * @param  decimal $montoRecibido  100.25
  * @param  integer $pago_id        16
  * @param  string  $f_pago         "2016/03/03"  
  * @return void
  *===========================================================================================*/
  public static function cobraCuotaExtraordinaria($periodo, $un_id, $montoRecibido, $pago_id, $f_pago) { 
    //dd($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('extra_siono', 1)
                  ->where('extra_pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    // dd($saldocpa);
    
    if ($datos) {
      foreach ($datos as $dato) {
        $importe= round(floatval($dato->extra),2);               
        
        if (($montoRecibido + $saldocpa) >= $importe) {
          // hay suficiente dinero para pagar por lo menos una cuota extraordinaria
          // por lo tanto, registra la cuota extraordinaria como pagada
          $dato->extra_pagada= 1;
          $dato->save();            

          if ($montoRecibido >= $importe) {
            // se recibio suficiente dinero para pagar por lo menos una cuota extraordinaria,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $importe, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1110.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, Catalogo::find(16)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);

            // Registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota extraordinaria de '. $dato->mes_anio, $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido= $montoRecibido- $importe;

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), $importe, $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, Catalogo::find(16)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);
            //dd($saldocpa, $sobrante);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota extraordinaria '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } elseif ($montoRecibido == Null && $pago_id == Null) {
            // si $montoRecibido y $pago_id son nulos, quiere decir que el sistema acaba de crear un nuevo periodo contable,
            // ejecuto la facturacion y esta tratando de utilizar la cuenta de pagos adelantados para cubrir por lo menos una cuota
            // de mantenimiento o recargo. En ese caso se emite una nota al propietario donde se le informa que se hizo uso de su cuenta de 
            // pagos por anticipados para cubrir la deuda, no es necesario emitir un recibo.  

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa - $importe;

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), $importe, $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, Catalogo::find(16)->nombre.' unidad '.$dato->ocobro, $importe, $un_id, $pago_id, Null, $dato->id);
            //dd($saldocpa, $sobrante);
       
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota extraordinaria '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $importe;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // registra en el diario
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo;
            $diario->fecha   = $f_pago; 
            $diario->detalle = Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9);
            $diario->debito  = $importe;
            $diario->credito = Null;
            $diario->save();
          
            // registra en el diario
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = Catalogo::find(16)->nombre.' unidad '.$dato->ocobro;
            $diario->debito = Null;
            $diario->credito = $importe;
            $diario->save();

            // registra en Ctdiario principal
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = 'Para registrar cobro de couta extraordinaria, unidad '.$dato->ocobro;
            $diario->save();

            // se envia notificacion via email, para eso encuentra todos los propietarios encargados de la unidad
            $props= Prop::where('un_id', $un_id)->where('encargado', 1)->get();
            
            // notifica a cada uno
            foreach ($props as $prop) {
              $nota = 'Para notificarle que, se descontó  de su cuenta de pagos anticipados un saldo de B/. '.number_format(($importe),2). ' para completar pago de la cuota extraordinaria de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);

              $user= User::find($prop->user_id);              
              $user->notify(new emailUsoDeCuentaAnticipados($nota, $user->nombre_completo));
            }

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } elseif ($montoRecibido < $importe) {
            // si el monto recibido es menor que el importe a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para completar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa = $saldocpa-($importe - $montoRecibido);

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $montoRecibido, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1110.00 "Cuotas de mantenimiento extraordinarias por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, Catalogo::find(16)->nombre.' unidad '.$dato->ocobro, $montoRecibido, $un_id, $pago_id, Null, $dato->id);
           
            // registra en Detallepago para generar un renglon en el recibo
            Self::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota de extraordinaria de '. $dato->mes_anio, $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), ($importe - $montoRecibido), $un_id, $pago_id);
            Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, Catalogo::find(16)->nombre.' unidad '.$dato->ocobro, ($importe - $montoRecibido), $un_id, $pago_id, Null, $dato->id);
            
            // salva un nuevo registro que representa una linea del recibo
            $dto = new Detallepago;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Estimado propietario, se descontó de su cuenta de pagos anticipados un saldo de B/. '.number_format($importe - $montoRecibido,2). ' para completar pago de cuota extraordinaria de '.$dato->ocobro.' quedando en saldo B/.'.number_format($saldocpa,2);
            $dto->monto = $montoRecibido;
            $dto->un_id = $un_id;
            $dto->tipo = 3;
            $dto->pago_id = $pago_id;
            $dto->save();

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = 0;    

          } else {
            return '<< ERROR >> en function cobraFacturas()';
          }
        } 
      }   // end foreach      
    } 
    
    //return round(floatval($montoRecibido),2);    
    return $montoRecibido; 
  }  // end function

  /** 
  *=============================================================================================
  * Registra en diario el resumen de las transacciones generadas producto del pago recibido
  * @param  string  $f_pago         "2016/03/03" 
  * @param  integer $periodo        3
  * @return void
  *===========================================================================================*/
  public static function registaEnDiario($pago_id, $periodo) {
    //dd($pago_id, $periodo);
  
    // encuentra todos los registros en ctmayores que pertenecen a un determinado pago
    $cuentas= Ctmayore::where('pago_id', $pago_id)
                      ->where('pcontable_id', $periodo)
                      ->where('anula', Null)
                      ->get();
    //dd($cuentas->toArray());
    
    // registra en el diario solo si encuentra algun registro
    if ($cuentas->count()) {
      // pasa cada uno de los registros encontrados al diario
      $i=0;
      foreach ($cuentas as $cuenta) {
        if ($i==0) {  // solo salva la fecha del pago si se trata del primer registro encontrado
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->fecha   = $cuenta->fecha; 
          $diario->detalle = $cuenta->detalle;
          $diario->debito  = $cuenta->debito ? $cuenta->debito : Null;
          $diario->credito = $cuenta->credito ? $cuenta->credito : Null;
          $diario->save();
        
        } else {
        // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = $cuenta->detalle;
          $diario->debito = $cuenta->debito ? $cuenta->debito : Null;
          $diario->credito = $cuenta->credito ? $cuenta->credito : Null;
          $diario->save();
        }
        $i=1;
      }

      // registra en Ctdiario principal
      $diario = new Ctdiario;
      $diario->pcontable_id  = $periodo;
      $diario->detalle = 'Para registrar transacciones producto del Pago No. '.$pago_id;
      $diario->save();
    } // endif

  } // fin de function 

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
  * @param  integer $pago_id        
  * @return void
  *===========================================================================================*/
   public static function anulaPago($pago_id) {
    dd('npago 695...',$pago_id);
    
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
  * @param  date/carbon $f_pago  +"date": "2016-02-04 00:00:00.000000"  - fecha en que se efectuo el pago  
  * @param  string      $un_id   "1"                                    - unidad que se desea penalizar individualmente
  * @return void
  *===========================================================================================*/
  public static function penalizarTipo2($f_pago, $un_id) {
    //dd($f_pago, $un_id);
    
    // clona $fecha para mantener su valor original
    $fpago = clone $f_pago;

    // penaliza individualmente a una determinada unidad
    $datos= Ctdasm::where('un_id', $un_id)
                ->whereDate('f_vencimiento','<', $fpago)
                ->where('pagada', 0)
                ->where('recargo_siono', 0)
                ->get();
    //dd($datos->toArray(), $f_pago, $un_id); 

    $i= 1;   
    
    // inicializa variable para almacenar el total de recargos
    $totalRecargos= 0;       

    if ($datos->count()) {
      foreach ($datos as $dato) {
        // determina a que periodo corresponde la fecha de vencimiento 
        $month= $fpago->month;    
        $year= $fpago->year;    

        $pdo= Sity::getMonthName($month).'-'.$year;
        $periodo= Pcontable::where('periodo', $pdo)->first()->id;
        //dd($periodo);       

        $dto = Ctdasm::find($dato->id);
        $dto->recargo_siono= 1;
        $dto->save();  

        // acumula el total de recargos
        $totalRecargos = $totalRecargos + $dato->recargo;
        
        // registra 'Recargo por cobrar en cuota de mantenimiento' 1130.00
        Sity::registraEnCuentas(
              $periodo,
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
              $periodo,
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
          $dto->pcontable_id  = $periodo;
          $dto->fecha   = Carbon::parse($dato->f_vencimiento)->addDay();
          $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
          $dto->debito  = $dato->recargo; 
          $dto->save(); 
        
        } else {
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id  = $periodo;
            $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
            $dto->debito  = $dato->recargo;
            $dto->save(); 
        }
        $i++;
      } // end foreach $datos 
      
      // registra en Ctdiario principal
      $dto = new Ctdiario;
      $dto->pcontable_id = $periodo;
      $dto->detalle = '   '.Catalogo::find(4)->nombre;
      $dto->credito  = $totalRecargos;
      $dto->save(); 

      // registra en Ctdiario principal
      $dto = new Ctdiario;
      $dto->pcontable_id = $periodo;
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


} //fin de Class Npago