<?php namespace App\library;

/*
|--------------------------------------------------------------------------
| Librería de códigos própios del sistema de inventario de Compras
|--------------------------------------------------------------------------
| Desarrollados y probados por:
| Germán Barrios
| sep 12, 2013
*/
use Event;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Mail;
use App\Mail\sendnuevoEcuentas;
use DB;
use App\Notifications\emailNuevaOcobro;
use App\Notifications\emailUsoDeCuentaAnticipados;

use App\Bitacora;
use App\User;
use App\Un;
use App\Ctdasm;
use App\Blqadmin;
use App\Ctdiario;
use App\Ctdiariohi;
use App\Detallepago;
use App\Pago;
use App\Ctmayore;
use App\Ctmayorehi;
use App\Pcontable;
use App\Catalogo;
use App\Ht;
use App\Detalledescuento;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Secapto;

class Sity {

  //ejemplo de cómo crear una variable publica a la clase
  //private static $ids = array();
  
  /****************************************************************************************
   * Este proceso se encarga de registrar el cobro por cuotas de mantenimiento o recargos,
   * también realiza los debidos asientos contables en cada una de las cuentas afectadas.
   *****************************************************************************************/
  public static function procesaPago($periodo, $un_id, $montoRecibido, $pago_id, $f_pago)
  {
    //dd($periodo, $montoRecibido);
    // Determina si la unidad tiene alguna facturacion pendiente por pagar
    $dato = Ctdasm::where('un_id', $un_id)
                  ->where('pcontable_id', '<=', $periodo)
                  ->where('pagada', 0)
                  ->orderBy('id', 'asc')
                  ->first();
    //dd($dato);

    if (!empty($dato)) {
      //Tiene facturacion pendiente por pagar.
      $sobrante = Sity::cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
      //dd($sobrante);
      
      //exista sobrante o no, el sistema tratara de cobrar recargos por pagos atrasados en caso de que exista uno o mas.
      //1. si existe un sobrante el sistema le adiciona el monto de la cuenta de pagos adelantados,
      //con este total tratara de cobrar todos los recargos que pueda.
      //2. si no existe sobrante alguno, el sistema utilizara solamente el monto de la cuenta de pagos anticipados
      //para cobrar todos los recargos que pueda.
      //dd($sobrante);
      Sity::cobraRecargos($periodo, $un_id, $sobrante, $pago_id, $f_pago);
    }

    else {
      // No tiene facturacion pendiente por pagar. Talves el propietario este tratando de pagar recargos por pagos atrasados.
      Sity::cobraRecargos($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
    }
  }

  /******************************************************************************************************
   * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
   *****************************************************************************************************/
  public static function cobraFacturas($periodo, $un_id, $montoRecibido, $pago_id, $f_pago)
  {   
    //dd($periodo, $montoRecibido);
    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Sity::getSaldoCtaPagosAnticipados($un_id, $periodo);    
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
            Sity::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota de mantenimiento de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido= $montoRecibido- $importe;

          } elseif ($montoRecibido == 0 && $pago_id) {
            // si el monto recibido es cero y existe un pago, entonces se depende en su totalidad de la cuenta
            // de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa -$importe;

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
            $saldocpa= $saldocpa -$importe;

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
            Sity::registraDetallepago($periodo, $dato->ocobro, 'Paga cuota de mantenimiento de '. $dato->mes_anio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

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

  /******************************************************************************************************
   * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible.
   *****************************************************************************************************/
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
    $saldocpa= Sity::getSaldoCtaPagosAnticipados($un_id, $periodo);    
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
    
          if ($montoRecibido>= $recargo) {
            // se recibio suficiente dinero para pagar por lo menos un recargo,
            // no hay necesidad de utilizar la cuenta de Pagos anticipados
            
            // registra un aumento en la cuenta 1010.00 "Cuentas por cobrar por recargo en cuotas de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $recargo, $un_id, $pago_id);

            // registra un disminucion en la cuenta 1130.00 "Cuentas por cobrar por recargo en cuota de mantenimiento" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' unidad '.$dato->ocobro, $recargo, $un_id, $pago_id, Null, $dato->id);

            // registra en Detallepago para generar un renglon en el recibo
            Sity::registraDetallepago($periodo, $dato->ocobro, 'Paga recargo en cuota de mantenimiento de '. $dato->mes_anio, $dato->id, $recargo, $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 2);

            // Actualiza el nuevo monto disponible para continuar pagando
            $montoRecibido = $montoRecibido - $recargo;    

          } elseif ($montoRecibido==0) {
            // si el monto recibido es cero, entonces 
            // se depende en su totalidad de la cuenta de Pagos anticipados para realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa -$recargo;

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

          } elseif ($montoRecibido< $recargo) {
            // si el monto recibido es menor que el recargo a pagar, 
            // quiere decir que se necesita hacer uso del saldo acumulado de la cuenta de Pagos anticipados para poder realizar el pago

            // disminuye el saldo de la cuenta Pagos anticipados
            $saldocpa= $saldocpa -($recargo-$montoRecibido);

            // registra un aumento en la cuenta Banco 
            Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$dato->ocobro, $montoRecibido, $un_id, $pago_id);    
          
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' unidad  '.$dato->ocobro, $montoRecibido, $un_id, $pago_id, Null, $dato->id);
           
            // registra en Detallepago para generar un renglon en el recibo
            Sity::registraDetallepago($periodo, $ocobro, 'Paga recargo en cuota de mantenimiento de '. $dato->mes_anio, $dato->id, $recargo, $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

            // registra una disminucion en la cuenta de Pagos anticipados
            Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.substr($dato->ocobro, 0, 9), ($recargo-$montoRecibido), $un_id, $pago_id);
            
            // registra un disminucion en la cuenta 1130.00 "Recargo en cuota de mantenimiento por cobrar" 
            Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, Catalogo::find(2)->nombre.' '.$dato->ocobro, ($recargo-$montoRecibido), $un_id, $pago_id);
            
            // registra en Detallepago para generar un renglon en el recibo
            Sity::registraDetallepago($periodo, $dato->ocobro, 'Se descuenta de su cuenta de pagos por anticipados', $dato->id, ($recargo-$montoRecibido), $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

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
    
    // verifica si se trata de un pago anticipado con el proposito de obtener descuento
    $montoRecibido= Sity::verificaDescuento($un_id, $montoRecibido, $pago_id, $periodo, $f_pago);

    // si al final de todo el proceso hay un sobrante entonces se registra como Pago anticipado
    if ($montoRecibido>0) {
      // registra pago recibido como un pago anticipado
      Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.Un::find($un_id)->codigo, $montoRecibido, $un_id, $pago_id);
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, Catalogo::find(5)->nombre.' unidad '.Un::find($un_id)->codigo, $montoRecibido, $un_id, $pago_id);

      // salva un nuevo registro que representa una linea del recibo
      $dto = new Detallepago;
      $dto->pcontable_id = $periodo;
      $dto->detalle = 'Estimado propietario, producto de contabilizar el pago realizado contra la totalidad adeudada a la fecha, el sistema ha detectado que existe un sobrante de B/. '.number_format($montoRecibido,2). ', por lo tanto el mismo será depositado en su cuenta de pagos anticipados. Este saldo usted lo podrá utilizar para completar futuros pagos. El nuevo saldo de su cuenta de pagos anticipados a la fecha es de B/.'.number_format(($saldocpa+$montoRecibido),2);
      $dto->monto = $montoRecibido;
      $dto->un_id = $un_id;
      $dto->tipo = 4;
      $dto->pago_id = $pago_id;
      $dto->save();
    }
  }  // end function


  /****************************************************************************************
   * Verifica si se trata de un pago anticipado con el proposito de obtener descuento
   *****************************************************************************************/
  public static function verificaDescuento($un_id, $montoRecibido, $pago_id, $periodo, $f_pago)
  {
    // verifica si la unidad tiene alguna deuda en el periodo actual o anteriores,
    // si la unidad tiene alguna deuda por pequena que sea no le permite participar en descuento
    
    // verifica si tiene alguna cuota de mantenimiento por pagar
    $dato = Ctdasm::where('un_id', $un_id)
                  ->where('pcontable_id', '<=', $periodo)
                  ->where('pagada', 0)
                  ->orderBy('id', 'asc')
                  ->first();
    
    // si encuentra alguna, cancela el proceso
    if ($dato) {
      return $montoRecibido;
    }
    
    // verifica si tiene algun recargo por pagar
    $dato = Ctdasm::where('pcontable_id','<', $periodo)
                   ->where('un_id', $un_id)
                   ->where('f_vencimiento','<', $f_pago)
                   ->where('recargo_siono', 1)
                   ->where('recargo_pagado', 0)
                   ->where('pagada', 1)
                   ->first();
    
    // si encuentra alguna, cancela el proceso
    if ($dato) {
      return $montoRecibido;
    }

    // verifica si la unidad tiene algun mes pagado por anticipado con descuento que aun no se han consumido
    $anticipado= Detalledescuento::where('consumido', 0)
                                 ->where('un_id', $un_id)
                                 ->orderBy('id', 'desc')->first();
    // dd($anticipado->toArray());  

    if ($anticipado) {
      // si encuentra alguno, entonces se toma la fecha como referencia para crear el nuevo mes con descuento    
      $f_periodo= $anticipado->fecha;

    } else {
      // si no encuentra ninguno, entoncer utiliza la fecha del periodo en que se hace el pago como referencia para crear el nuevo mes con descuento
      $f_periodo= Pcontable::find($periodo)->fecha;    
    }

    // encuentra las generales de la unidad
    $un= Un::find($un_id);

    // encuentra las generales de la seccion a la cual pertenece la unidad
    $seccion= Secapto::find($un->seccione_id);  
    //dd($seccion->toArray());
    
    // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
    $saldocpa= Sity::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //dd($saldocpa);

    // calcula la cantidad de meses que se podrian pagar con el $montoRecibido
    $meses= intdiv(($montoRecibido + $saldocpa), ($seccion->cuota_mant-$seccion->descuento));
    //dd($meses, $montoRecibido, ($seccion->cuota_mant - $seccion->descuento));
    
    // determina si se aplica descuento de acuerdo con la normativa del ph
    if ($meses >= $seccion->m_descuento) {
      // aplica descuento
      
      // calcula el total de dinero a contabilizar 
      $totalContabilizar= $seccion->m_descuento * ($seccion->cuota_mant-$seccion->descuento);
      
      // registra un aumento en la cuenta Banco 
      Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$un->codigo, $totalContabilizar, $un_id, $pago_id);    
      
      // registra una disminucion en la cuenta de Pagos anticipados
      Sity::registraEnCuentas($periodo, 'mas', 2, 5, $f_pago, Catalogo::find(5)->nombre.' '.$un->codigo, $totalContabilizar, $un_id, $pago_id);
      
      $totalUtilizado = 0;    

      // registra en la tabla detalledescuentos el desglose de los meses a los que se les aplicara el descuento
      for ($x= 1; $x<= $meses; $x++) {
        // genera los datos para el primer renglon o mes que tendra descuento
        $f_periodo= Carbon::parse($f_periodo)->addMonth();
        
        $year= $f_periodo->year;
        $month= $f_periodo->month;
        $day= $seccion->d_registra_cmpc;
        //dd($year, $month, $day);        
        
        $f_periodo= Carbon::createFromDate($year, $month, $day);
        $mes_anio= Sity::getMonthName($month).'-'.$year;
        //$periodo++;  
        
        if ($montoRecibido >= ($seccion->cuota_mant - $seccion->descuento)) {
          // se recibio suficiente dinero para pagar por lo menos una cuota con descuento,
          // no hay necesidad de utilizar la cuenta de Pagos anticipados
        
          $dto = new Detalledescuento;
          $dto->un_id = $un_id;
          $dto->fecha = $f_periodo;
          $dto->pcontable_id = $periodo;
          $dto->mes_anio = $mes_anio;
          $dto->detalle = 'Paga cuota de mantenimiento de '.$mes_anio.' por adelantado';
          $dto->importe = $seccion->cuota_mant - $seccion->descuento;
          $dto->descuento = $seccion->descuento; 
          $dto->pago_id = $pago_id;
          $dto->save();          

          // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 1, $f_pago, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);        
          // registra un aumento en la cuenta Banco 
          //Sity::registraEnCuentas($periodo, 'mas', 4, 3, $f_pago, Catalogo::find(3)->nombre, $seccion->cuota_mant, $un_id, $pago_id);    
          
          // registra un aumento en "Gastos por cuentas incobrables"
          //Sity::registraEnCuentas($periodo, 'mas', 6, 13, $f_pago, Catalogo::find(13)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->descuento, $un_id, $pago_id);    
          // registra un aumento en la cuenta Banco 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$un->codigo.' '.$mes_anio, ($seccion->cuota_mant - $seccion->descuento), $un_id, $pago_id);    
            
          // registra un disminucion en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' unidad '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);
          
          // Registra en Detallepago para generar un renglon en el recibo
          Sity::registraDetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);
        
          // Actualiza el nuevo monto disponible para continuar pagando
          $montoRecibido = $montoRecibido - ($seccion->cuota_mant - $seccion->descuento); 
          $mostrarNota = false;

        } elseif (($montoRecibido + $saldocpa) >= ($seccion->cuota_mant - $seccion->descuento)) {
          // si la suma del $montorecibido mas el $saldocpas es suficiente para pagar una cuota con descuento,
          // se hace lo siguiente:
          //-----------------------------------------------------------------
          // 1. se consume en su totalidad el montoRecibido
          //-----------------------------------------------------------------
          
          $dto = new Detalledescuento;
          $dto->un_id = $un_id;
          $dto->fecha = $f_periodo;
          $dto->pcontable_id = $periodo;
          $dto->mes_anio = $mes_anio;
          $dto->detalle = 'Paga cuota de mantenimiento de '.$mes_anio.' por adelantado';
          $dto->importe = $seccion->cuota_mant - $seccion->descuento;
          $dto->descuento = $seccion->descuento; 
          $dto->pago_id = $pago_id;
          $dto->save();  

          // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 1, $f_pago, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);        
          // registra un aumento en la cuenta Banco 
          //Sity::registraEnCuentas($periodo, 'mas', 4, 3, $f_pago, Catalogo::find(3)->nombre, $seccion->cuota_mant, $un_id, $pago_id);    

          // registra un aumento en la cuenta Banco 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$un->codigo.' '.$mes_anio, $montoRecibido, $un_id, $pago_id);    

          //-----------------------------------------------------------------
          // 2. descuenta la diferencia de la cuenta de pagos anticipados
          //-----------------------------------------------------------------
          // calcula el total a descontar de la cuenta de pagos anticipados necesarios para completar el pago del mes con descuento
          $totalDescontarPa = (($seccion->cuota_mant - $seccion->descuento) - $montoRecibido);
          // registra una disminucion en la cuenta de Pagos anticipados
          //Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' '.$un->codigo.' '.$mes_anio, $totalDescontarPa, $un_id, $pago_id);

          // registra un aumento en "Gastos por cuentas incobrables" 
          //Sity::registraEnCuentas($periodo, 'mas', 6, 13, $f_pago, Catalogo::find(13)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->descuento, $un_id, $pago_id);    
          // registra un disminucion en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);

          // Registra en Detallepago para generar un renglon en el recibo
          Sity::registraDetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

          // total utilizado de la cuenta de pagos por anticipados
          $totalUtilizado = $totalUtilizado + $totalDescontarPa; 

          // Actualiza el nuevo monto de la cuenta de pagos por anticipado
          $saldocpa = $saldocpa - $totalDescontarPa;  
          
          // actualiza el saldo del monto recibido
          $montoRecibido= 0;
          $mostrarNota = true;

        } elseif ($montoRecibido = 0 && $saldocpa >= ($seccion->cuota_mant - $seccion->descuento)) {
          // si el monto de $montorecibido es cero y hay suficiente dinero para pagar un cuota de mantenimiento
          // utilizando solamente la cuenta de pagos anticipados se hace lo siguiente:
        
          $dto = new Detalledescuento;
          $dto->un_id = $un_id;
          $dto->fecha = $f_periodo;
          $dto->pcontable_id = $periodo;
          $dto->mes_anio = $mes_anio;
          $dto->detalle = 'Paga cuota de mantenimiento de '.$mes_anio.' por adelantado';
          $dto->importe = $seccion->cuota_mant - $seccion->descuento;
          $dto->descuento = $seccion->descuento; 
          $dto->pago_id = $pago_id;
          $dto->save();  

          // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 1, $f_pago, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);        
          // registra un aumento en la cuenta Banco 
          //Sity::registraEnCuentas($periodo, 'mas', 4, 3, $f_pago, Catalogo::find(3)->nombre, $seccion->cuota_mant, $un_id, $pago_id);    
         
          // calcula el total a descontar de la cuenta de pagos anticipados necesarios para completar el pago del mes con descuento
          $totalDescontarPa = ($seccion->cuota_mant - $seccion->descuento);
          // registra una disminucion en la cuenta de Pagos anticipados
          //Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' '.$un->codigo.' '.$mes_anio, $totalDescontarPa, $un_id, $pago_id);
          // registra un aumento en "Gastos por cuentas incobrables" 
          //Sity::registraEnCuentas($periodo, 'mas', 6, 13, $f_pago, Catalogo::find(13)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->descuento, $un_id, $pago_id);    
          // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
          //Sity::registraEnCuentas($periodo, 'mas', 1, 1, $f_pago, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$mes_anio, $seccion->cuota_mant, $un_id, $pago_id);        
          
          // Registra en Detallepago para generar un renglon en el recibo
          Sity::registraDetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Sity::getLastNoDetallepago($pago_id), 1);

          // total utilizado de la cuenta de pagos por anticipados
          $totalUtilizado = $totalUtilizado + $totalDescontarPa; 

          // Actualiza el nuevo monto de la cuenta de pagos por anticipado
          $saldocpa = $saldocpa - $totalDescontarPa;  
          $mostrarNota = true;
        } // end second if
      } // end for
      
      if ($mostrarNota) {
        // salva un nuevo registro que representa una linea del recibo
        $dto = new Detallepago;
        $dto->pcontable_id = $periodo;
        $dto->detalle = 'Estimado propietario, se desconto de su cuenta de pagos anticipado un total de B/. '.number_format(($totalUtilizado),2). ' para completar pago de cuotas de mantenimiento por anticipado quedando en saldo B/.'.number_format($saldocpa,2);
        $dto->monto = $totalUtilizado;
        $dto->un_id = $un_id;
        $dto->tipo = 3;
        $dto->pago_id = $pago_id;
        $dto->save();  
      }

      // notifica al propietario el uso de su cuenta de pagos por anticipados



      // recalcula sobrante
      //$montoRecibido= $montoRecibido-(($seccion->cuota_mant-$seccion->descuento)*$meses);
    } // end first if
    
    return $montoRecibido;
  }

  /****************************************************************************************
   * Encuentra el saldo actual de una cuenta de acuerdo al tipo de cuenta y periodo contable
   *****************************************************************************************/
  public static function getSaldoCuenta($cuenta, $pcontable_id)
  {
  
    // encuentra el tipo de cuenta
    $tipo= Catalogo::find($cuenta)->tipo;

    $tdebito= Ctmayore::where('cuenta', $cuenta)
                ->where('pcontable_id', $pcontable_id)
                ->sum('debito');
    $tdebito= round(floatval($tdebito),2);
    
    $tcredito= Ctmayore::where('cuenta', $cuenta)
                ->where('pcontable_id', $pcontable_id)
                ->sum('credito');
    $tcredito= round(floatval($tcredito),2);    
    
    if ($tipo==1 || $tipo==6) {  
      $sa= $tdebito-$tcredito;

    } elseif ($tipo==2 || $tipo==3 || $tipo==4) {  
      $sa= $tcredito-$tdebito;
    
    } else {
      return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
    } 

    // si no tiene saldo, iniciliza en cero
    $sa = ($sa) ? $sa : 0;
    //dd($sa);    
    return $sa;
  }

  /****************************************************************************************
   * Encuentra el saldo actual de la cuenta de pagos anticipado de una unidad en especial
   *****************************************************************************************/
  public static function getSaldoCtaPagosAnticipados($un_id, $periodo)
  {
    $sa= 0;
    
    // encuentra el total de pagos anticipados tomando en cuenta y determinado periodo y los periodo anteriores al mismo
    if($periodo) {
      $tcredito= Ctmayore::where('un_id', $un_id)
                  ->where('pcontable_id', '<=',$periodo)
                  ->where('cuenta', 5)
                  ->sum('credito');

      $tdebito= Ctmayore::where('un_id', $un_id)
                  ->where('pcontable_id', '<=',$periodo)
                  ->where('cuenta', 5)
                  ->sum('debito');
      //dd($tcredito, $tdebito);        
      
      $sa= round(floatval($tcredito),2) - round(floatval($tdebito),2);
    
    } else {
      
      //encuentra todos los periodos contables que no este cerrados
      $periodos= Pcontable::where('cerrado', 0)
                       ->orderBy('id')
                       ->get();
      //dd($periodos);
      
      // calcula el total de pagos anticipados de todos los periodos abiertos
      foreach ($periodos as $periodo) {
        $tcredito= Ctmayore::where('un_id', $un_id)
                    ->where('pcontable_id', '=', $periodo->id)
                    ->where('cuenta', 5)
                    ->sum('credito');

        $tdebito= Ctmayore::where('un_id', $un_id)
                    ->where('pcontable_id', '=', $periodo->id)
                    ->where('cuenta', 5)
                    ->sum('debito');
        //dd($tcredito, $tdebito);  
        
        $sa= $sa+ round(floatval($tcredito),2) - round(floatval($tdebito),2);
      }
    }

    // si no tiene saldo, iniciliza en cero
    $sa = ($sa) ? $sa : 0;
    //dd($sa);    
    return $sa;
  }

  /****************************************************************************************
   * Registra en ctmayores
   ****************************************************************************************/
  public static function registraEnCuentas($pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id=Null, $pago_id=Null, $org_id=Null, $ctdasm_id=Null, $anula=Null)
  {

    $debito = 0;
    $credito = 0;
    //dd($cuenta);

    // encuentra las generales de la cuenta
    $cta = Catalogo::find($cuenta);
    //dd($cta->toArray());      
  
    // encuentra saldos
    $saldocta= Sity::getSaldoCuenta($cuenta, $pcontable_id);

    // determina si los saldos aumentan o disminuyen en cuentas permanentes
    if (($tipo==1 || $tipo==6) && $mas_menos=='mas') {  
      //$saldocta= $saldocta+$monto;
      $debito = $monto;
      
    } elseif (($tipo==1 || $tipo==6) && $mas_menos=='menos') {  
      //$saldocta= $saldocta-$monto;
      $credito =abs($monto);
    
    // determina si los saldos aumentan o disminuyen en cuentas temporales
    } elseif (($tipo==2 || $tipo==3 || $tipo==4) && $mas_menos=='mas') {  
      //$saldocta= $saldocta+$monto;
      $credito =$monto;    

    } elseif (($tipo==2 || $tipo==3 || $tipo==4) && $mas_menos=='menos') {  
      //$saldocta= $saldocta-$monto;
      $debito = abs($monto);
    
    } else {
      return 'Error: tipo de cuenta no exite en function Sity::registraEnCuentas()';
    } 

    // agrega el nuevo registro al modelo ctmayore
    $dato = new Ctmayore;
    $dato->pcontable_id     = $pcontable_id;
    $dato->tipo             = $tipo;
    $dato->cuenta           = $cuenta;
    $dato->codigo           = $cta->codigo;
    $dato->fecha            = $fecha;
    $dato->detalle          = $detalle;
    $dato->debito           = $debito;
    $dato->credito          = $credito;
    $dato->un_id            = $un_id;
    $dato->org_id           = $org_id; 
    $dato->pago_id          = $pago_id; 
    $dato->ctdasm_id        = $ctdasm_id;
    $dato->anula            = $anula;
    $dato->save();
  }   

  /*************************************************************************************************
   * Esta function registra en la tabla detallepagos cada transaccion que se genera de un pago
   *************************************************************************************************/
  public static function registraDetallepago($periodo, $ocobro, $detalle, $ref, $monto, $un_id, $pago_id, $no, $tipo)
  {
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
  
   /****************************************************************************************
   * Encuentra el ultimo renglon de un determinado pago
   *****************************************************************************************/
  public static function getLastNoDetallepago($pago_id)
  {
    $dato = Detallepago::where('pago_id', $pago_id)
                       ->orderBy('no', 'desc')
                       ->first();
    $no = ($dato) ? floatval($dato->no)+1 : 1;
    return $no;
  }  

  /****************************************************************************************
   * Anula un pago efectuado con cheque
   *****************************************************************************************/
  public static function anulaPagoCheque($pago_id)
  {
    $pago=Pago::find($pago_id);
    $pago->anulado = 1;
    $pago->entransito = 0;
    $pago->save(); 
    return; 
  }

  /****************************************************************************************
   * Anula un pago
   *****************************************************************************************/
  public static function anulaPago($pago_id)
  {
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

  /*****************************************************************************************
   * Determina el nombre del mes.
   *****************************************************************************************/
  public static function getMonthName($month) {
      switch ($month) {
          case 1:
              $monthName='Ene';
              break;
          case 2:
              $monthName='Feb';
              break;
          case 3:
              $monthName='Mar';
              break;
          case 4:
              $monthName='Abr';
              break;
          case 5:
              $monthName='May';
              break;
          case 6:
              $monthName='Jun';
              break;       
         case 7:
              $monthName='Jul';
              break;
          case 8:
              $monthName='Ago';
              break;
          case 9:
              $monthName='Sep';
              break;
          case 10:
              $monthName='Oct';
              break;
          case 11:
              $monthName='Nov';
              break;
          case 12:
              $monthName='Dic';
              break;       
      }
  return $monthName;
  }
 
  /*****************************************************************************************
   *  Registra en Bitacoras.
   *****************************************************************************************/
  public static function RegistrarEnBitacora($accione_id, $tabla, $registro=Null, $detalle) {
      $bitacora = new Bitacora;
      $bitacora->fecha           = Carbon::today();       
      $bitacora->hora            = Carbon::now('America/Panama');
      $bitacora->accione_id      = $accione_id;
      $bitacora->tabla           = $tabla;
      $bitacora->registro        = $registro;            
      $bitacora->detalle         = $detalle;
      if (Auth::check()) {
          $bitacora->user_id     = Auth::user()->id;            
      }
      $bitacora->ip              = $_SERVER["REMOTE_ADDR"];
      $bitacora->save();
      return 'nada';
  }    

  /****************************************************************************************
   * Esta function encuentra el user_id del administrador encargado del bloque al cual
   * pertenece la seccion en estudio
   *****************************************************************************************/
  public static function findBlqadmin($bloque_id)
  {
    //Encuentra el administrador encargado del bloque al cual pertenece la seccion
    $bug=Blqadmin::where('encargado', '1')
                ->where('bloque_id', $bloque_id)
                ->first();
    return $bug->user_id;
  }


  /****************************************************************************************
   * Arma un arreglo con la informacion necesaria para confeccionar
   * la hoja de trabajo de un determinado periodo contable
   *****************************************************************************************/
  public static function getDataParaHojaDeTrabajo($periodo)
  {
    //dd($periodo);    

    $data=array();    
    $i=0;   

    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $cuentas= Ctmayore::where('pcontable_id', $periodo)->where('cuenta','!=', 5)->select('cuenta')->get();
    //dd($cuentas->toArray());
    
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas excluyendo la cuenta no 5 de Pagos anticipados ya
    // que es una cuenta compartida por muchas unidades, por lo tanto hay que calcularle el saldo
    // a cada unidad por separado. Este proceso se hace al final del foreach
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());

      // Calcula el saldo de la cuenta tomando en cuenta el periodo y eliminado los ajustes hechos a la misma      
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('ajuste_siono', 0)
                    ->sum('debito');
      //dd($totalDebito);
      
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('ajuste_siono', 0)
                    ->sum('credito');
      //dd($totalCredito);
      
      // Arma un arreglo con la informacion de las cuenta en estudio
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;      
      $data[$i]["tipo"]= $cta->tipo;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["clase"]= $cta->corriente_siono;
      $data[$i]["cta_nombre"]= $cta->nombre;
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;

      // clasifica el saldo actual de la cuenta en estudio para determinar si el mismo es tipo debito o credito
      if ($cta->tipo==1) {
        $saldo= floatval($totalDebito)-floatval($totalCredito);
        $data[$i]["saldo_debito"]= $saldo;
        $data[$i]["saldo_credito"]= 0;
        
        $data[$i]["saldoAjustado_debito"]= $saldo;
        $data[$i]["saldoAjustado_credito"]= 0;

        $data[$i]["bg_debito"]= $saldo;
        $data[$i]["bg_credito"]= 0;      

      } elseif ($cta->tipo==6) {
        $saldo= floatval($totalDebito)-floatval($totalCredito);
        $data[$i]["saldo_debito"]= $saldo;
        $data[$i]["saldo_credito"]= 0;
        
        $data[$i]["saldoAjustado_debito"]= $saldo;
        $data[$i]["saldoAjustado_credito"]= 0;
      
        $data[$i]["er_debito"]= $saldo;
        $data[$i]["er_credito"]= 0;

      } elseif ($cta->tipo==2) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;

        $data[$i]["bg_debito"]= 0;
        $data[$i]["bg_credito"]= $saldo;

      } elseif ($cta->tipo==3) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;      
      
        $data[$i]["bg_debito"]= 0;
        $data[$i]["bg_credito"]= $saldo; 

      } elseif ($cta->tipo==4) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;

        $data[$i]["er_debito"]= 0;
        $data[$i]["er_credito"]= $saldo;
      }

      //verifica si la cuenta en estudio tuvo ajustes
      $ajustes= Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 

        // clasifica el total de ajuste hechos a la cuenta de acuerdo a si es tipo debito o credito
        if ($cta->tipo==1) {
          $totalAjuste= floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= $totalAjuste;
            $data[$i]["saldoAjuste_credito"]= 0;          
            
            $data[$i]["saldoAjustado_debito"]= $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"]= 0;           
          
            $data[$i]["bg_debito"]= $saldo + $totalAjuste;
            $data[$i]["bg_credito"]= 0;    

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= abs($totalAjuste);

            $data[$i]["saldoAjustado_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"]= 0;
          
            $data[$i]["bg_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["bg_credito"]= 0;
          }
        
        } elseif ($cta->tipo==6) {
          $totalAjuste= floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= $totalAjuste;
            $data[$i]["saldoAjuste_credito"]= 0;          
            
            $data[$i]["saldoAjustado_debito"]= $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"]= 0;           
          
            $data[$i]["er_debito"]= $saldo + $totalAjuste;
            $data[$i]["er_credito"]= 0;        

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= abs($totalAjuste);

            $data[$i]["saldoAjustado_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"]= 0;
          
            $data[$i]["er_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["er_credito"]= 0;
          }        

        } elseif ($cta->tipo==2) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste; 

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);
          
            $data[$i]["bg_debito"]= 0; 
            $data[$i]["bg_credito"]= $saldo - abs($totalAjuste);
          }
        
        } elseif ($cta->tipo==3) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
            
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste;              
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= abs($saldo - abs($totalAjuste));
            $data[$i]["saldoAjustado_credito"]= 0; 

            $data[$i]["bg_debito"]= abs($saldo - abs($totalAjuste));
            $data[$i]["bg_credito"]= 0;
          }

        } elseif ($cta->tipo==4) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["er_debito"]= 0;
            $data[$i]["er_credito"]= $saldo + $totalAjuste;

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);

            $data[$i]["er_debito"]= 0; 
            $data[$i]["er_credito"]= $saldo - abs($totalAjuste);
          }
        }
      }
      $i++;    
    }
    
    // procesa individualmente cada una de las cuentas que comparten la cuenta de  Pagos anticipados 
    $uns= Ctmayore::where('pcontable_id', $periodo)
                  ->where('cuenta', 5)
                  ->select('un_id')->get();
    //dd($uns->toArray());
    
    $uns= $uns->unique('un_id');
    //dd($uns->toArray());    
    
    // procesa cada una de las unidades que tubieron Pagos anticipados en el periodo
    foreach ($uns as $un) {

      // encuentra las generales de la cuenta
      $cta= Catalogo::find(5);
      //dd($cta->toArray());

      // encuentra el codigo de la unidad
      $cod= Un::find($un->un_id)->codigo;
      
      // Calcula el saldo de la cuenta tomando en cuenta el periodo y eliminado los ajustes hechos a la misma      
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('un_id', $un->un_id)
                    ->where('ajuste_siono', 0)
                    ->sum('debito');
      //dd($totalDebito);
      
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('un_id', $un->un_id)
                    ->where('ajuste_siono', 0)
                    ->sum('credito');
      //dd($totalCredito);
      
      // Arma un arreglo con la informacion de las cuenta en estudio
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;      
      $data[$i]["tipo"]= $cta->tipo;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["clase"]= $cta->corriente_siono;
      $data[$i]["cta_nombre"]= $cta->nombre.' '.$cod;
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;

      // coloca el saldo de la cuenta sin ajustes
      $saldo= floatval($totalCredito)-floatval($totalDebito);
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= $saldo;
      
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= $saldo;

      $data[$i]["bg_debito"]= 0;
      $data[$i]["bg_credito"]= $saldo;

      //verifica si la cuenta en estudio tuvo ajustes
      $ajustes= Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->where('un_id', $un->un_id)
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('un_id', $un->un_id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('un_id', $un->un_id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 
        
        // clasifica el total de ajuste hechos a la cuenta de acuerdo a si es tipo debito o credito
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste; 

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);
          
            $data[$i]["bg_debito"]= 0; 
            $data[$i]["bg_credito"]= $saldo - abs($totalAjuste);
          }
      }
      $i++;  
    }    

    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    
    //dd($data);
    return $data;
  }

  /****************************************************************************************
   * Arma un arreglo con la informacion necesaria para confeccionar
   * el Estado de resultado de un determinado periodo contable
   *****************************************************************************************/
  public static function getDataParaEstadoResultado($periodo, $tipo)
  {
    $data= array();    
    $i= 0;   
    
    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $cuentas= Ctmayore::where('pcontable_id', $periodo)
                    ->where('tipo', $tipo)
                    ->select('cuenta')->get();
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());
      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);
      // si la cuenta no tuvo actividad la ignora
      $saldo= floatval($totalDebito) - floatval($totalCredito);
      if ($saldo!=0) {
        // Arma un arreglo con la informacion de las cuenta en estudio
        //$data[$i]["id"]= $datos->id;
        $data[$i]["periodo"]= $periodo;
        $data[$i]["cuenta"]= $cta->id;
        $data[$i]["codigo"]= $cta->codigo;
        $data[$i]["cta_nombre"]= $cta->nombre;
        
        if ($tipo==6) {
          $totalAjuste= floatval($totalDebito) - floatval($totalCredito);
          //dd($totalAjuste);
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= $totalAjuste;
            $data[$i]["saldo_credito"]= "";         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;        
          }
        } elseif ($tipo==4) {
          $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
          //dd($totalAjuste);
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]=  $totalAjuste;
            $data[$i]["saldo_credito"]= "";       
          }
        } else {
          return 'Error: tipo de cuenta invalido en function Sity::getDataParaBalanceGeneral()';
        }  
      } 
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    //dd($data);
    
    return $data;
  }

  /****************************************************************************************
   * Arma un arreglo con la informacion de las cuentas tipo activos o pasivos, corriente o 
   * no corrientes necesaria para confeccionar el Balance general de un determinado periodo contable
   *****************************************************************************************/
  public static function getDataParaBalanceGeneral($periodo, $tipo, $corriente_siono=Null)
  {
    $data=array();    
    $i=0;   

    // Encuentra todas las cuentas activas en ctmayores de un determinado periodo y tipo de cuenta
    if ($tipo==1 || $tipo==2) {
      $cuentas=Ctmayore::where('pcontable_id', $periodo)
                ->where('ctmayores.tipo', $tipo)
                ->join('catalogos','catalogos.id','=','ctmayores.cuenta')
                ->where('catalogos.corriente_siono', $corriente_siono)
                ->select('cuenta')
                ->get();

    } elseif ($tipo==3) {
      $cuentas=Ctmayore::where('pcontable_id', $periodo)
              ->where('ctmayores.tipo', $tipo)
              ->select('cuenta')
              ->get();
    } else {
      return 'Error: tipo de cuenta invalido en function Sity::getDataParaBalanceGeneral()';
    }   
    
    $cuentas=$cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());

      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);

      // Arma un arreglo con la informacion de las cuenta en estudio
      //$data[$i]["id"]= $datos->id;
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["cta_nombre"]= $cta->nombre;
      
      if ($tipo==1) {
        $totalAjuste= floatval($totalDebito) - floatval($totalCredito);
        if ($totalAjuste>0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";         
        
        } elseif ($totalAjuste<0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";        
        
        } elseif ($totalAjuste==0) {
          // si es igual a cero
          $data[$i]["saldo_debito"]= 0;
          $data[$i]["saldo_credito"]= "";       
        }

      } elseif ($tipo==2 || $tipo==3) {
        $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
        if ($totalAjuste>0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= "";
          $data[$i]["saldo_credito"]= $totalAjuste;         
        
        } elseif ($totalAjuste<0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= "";
          $data[$i]["saldo_credito"]= $totalAjuste;       
        
        } elseif ($totalAjuste==0) {
          // si es igual a cero
          $data[$i]["saldo_debito"]=  "";
          $data[$i]["saldo_credito"]= 0;       
        }
      
      } else {
        return 'Error: tipo de cuenta invalido en function Sity::getDataParaBalanceGeneral()';
      }
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
 
    return $data;
  }

  /****************************************************************************************
  * Esta function inicializa en el nuevo periodo todas las cuentas permanentes
  * con el saldo del periodo anterior activas presentes en el catalogo de cuentas
  *****************************************************************************************/
  public static function inicializaCuentasPerm($pcontable_id, $fecha)
  {
      
      // encuentras los datos, los mismos datos que se utilizaron para la Hoja de trabajo
      $datos= Sity::getDataParaHojaDeTrabajo($pcontable_id);
      //dd($datos);
      
      $i=1;
      foreach($datos as $dato) {
        if ($dato['tipo']==1) {
          // registra en la tabla ctmayores
          $data = new Ctmayore;
          $data->pcontable_id     = $pcontable_id+1;
          $data->tipo             = $dato['tipo'];
          $data->cuenta           = $dato['cuenta'];
          $data->codigo           = $dato['codigo'];
          $data->fecha            = $fecha;
          $data->detalle          = $dato['cta_nombre'];
          $data->debito           = $dato['saldoAjustado_debito'];
          $data->credito          = $dato['saldoAjustado_credito'];
          $data->save();
          
          if ($i==1) {
            // registra en Ctdiario principal
            $data = new Ctdiario;
            $data->pcontable_id  = $pcontable_id+1;
            $data->fecha         = $fecha;
            $data->detalle = $dato['cta_nombre'];
            $data->debito  = $dato['saldoAjustado_debito'];
            $data->credito = Null;
            $data->save();

          } else {
            // registra en Ctdiario principal
            $data = new Ctdiario;
            $data->pcontable_id  = $pcontable_id+1;
            $data->detalle = $dato['cta_nombre'];
            $data->debito  = $dato['saldoAjustado_debito'];
            $data->credito = Null;
            $data->save();
          }
          $i++;

        } elseif(($dato['tipo']==2 && $dato['cuenta']!=5) || $dato['tipo']==3) {
          // se excluye la cuenta 5 2010.00 "Anticipos o avances recibidos de propietarios (Pasivo diferido)"
          // ya que es una cuenta que comparten diferentes unidades. La inicializacion tiene un trato especial debido
          // a que como es una cuenta compartida se debe inicializar un saldo por cada unidad que comparta esta cuenta.
          
          // registra en la tabla ctmayores
          $data = new Ctmayore;
          $data->pcontable_id     = $pcontable_id+1;
          $data->tipo             = $dato['tipo'];
          $data->cuenta           = $dato['cuenta'];
          $data->codigo           = $dato['codigo'];
          $data->fecha            = $fecha;
          $data->detalle          = $dato['cta_nombre'];
          $data->debito           = $dato['saldoAjustado_debito'];
          $data->credito          = $dato['saldoAjustado_credito'];
          $data->un_id            = 0;
          $data->save();
          
          // registra en Ctdiario principal
          $data = new Ctdiario;
          $data->pcontable_id  = $pcontable_id+1;
          $data->detalle = $dato['cta_nombre'];
          $data->debito  =  Null;
          $data->credito = $dato['saldoAjustado_credito'];
          $data->save();  
        }
      }
    
    // inicializacion especial de la cuenta 5  2010.00 "Anticipos o avances recibidos de propietarios (Pasivo diferido)"

    // Encuentra todas las cuentas cuenta 5  2010.00 activas en ctmayores para un determinado periodo
    $cuentas=Ctmayore::where('pcontable_id', $pcontable_id)->where('cuenta', 5)->get();
    //dd($cuentas->toArray());
    
    $uns=$cuentas->unique('un_id');
    //dd($uns->toArray());
      
    // procesa cada una de las unidades con saldo en la cuenta 5 2010.00 encontradas
    foreach ($uns as $un) {

      // Encuentra saldo a favor en cuenta 2010.00 No. 5 "Anticipos y avances recibidos de propietarios" 
      $saldocpa=Sity::getSaldoCtaPagosAnticipados($un->un_id, $pcontable_id);
      //dd($saldocpa);
      
      // registra en la tabla ctmayores
      $data = new Ctmayore;
      $data->pcontable_id     = $pcontable_id+1;
      $data->tipo             = $un->tipo;
      $data->cuenta           = $un->cuenta;
      $data->codigo           = $un->codigo;
      $data->fecha            = $fecha;
      $data->detalle          = Catalogo::find(5)->nombre.', '.Un::find($un->un_id)->codigo;
      $data->debito           = 0;
      $data->credito          = $saldocpa;
      $data->un_id            = $un->un_id;
      $data->save();

      // registra en Ctdiario principal
      $data = new Ctdiario;
      $data->pcontable_id  = $pcontable_id+1;
      $data->detalle = Catalogo::find(5)->nombre.', '.Un::find($un->un_id)->codigo;
      $data->debito  =  Null;
      $data->credito = $saldocpa;
      $data->save();  
    }
  
  // registra la utilidad en el diario del periodo posterior
  $data = new Ctdiario;
  $data->pcontable_id     = $pcontable_id+1;
  $data->detalle          = 'Para registrar aperturas de cuentas permanentes y utilidad neta del periodo anterior '.Pcontable::find($pcontable_id)->periodo;
  $data->save(); 

  } 

  /***********************************************************************************
  * Calcula la Utilidad neta de un periodo en especial
  ************************************************************************************/ 
  public static function getUtilidadNeta($pcontable_id) {
    // encuentra todas las cuentas de Ingresos de un determinado periodo contable
    $ingresos= Sity::getDataParaEstadoResultado($pcontable_id, 4);

    // encuentra todas las cuentas de Gastos de un determinado periodo contable
    $gastos= Sity::getDataParaEstadoResultado($pcontable_id, 6);
            
    //calcula el total de la columna debito y el de la columna credito
    $totalIngresos = 0;
    $totalGastos = 0;
    
    // calcula en total de ingresos recibidos         
    foreach($ingresos as $ingreso) {
      $totalIngresos += $ingreso['saldo_credito'];
    }        
    
    foreach($gastos as $gasto) {
      // totales balance ajustado        
      $totalGastos += $gasto['saldo_debito'];
    }
   
    // calcula la utilidad neta
    $utilidad= $totalIngresos - $totalGastos;
    return $utilidad;
  }


//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
// funciones para hacer pruebas de facturacion, se debe eliminar cuando el sistema este en produccion
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

/****************************************************************************************
* Esta function limpia todas las tablas que que tienen relacion con la contabilidad
*****************************************************************************************/
public static function limpiar()
{
  DB::statement('SET FOREIGN_KEY_CHECKS=0;');
  
  DB::table('ctdasms')->truncate(); 
  DB::table('detallepagos')->truncate();
  DB::table('pagos')->truncate();
  DB::table('detallepagofacturas')->truncate();
  DB::table('ctmayores')->truncate();
  DB::table('ctmayorehis')->truncate();   
  DB::table('ctdiarios')->truncate();
  DB::table('ctdiariohis')->truncate(); 
  DB::table('facturas')->truncate();
  DB::table('detallefacturas')->truncate();
  DB::table('pcontables')->truncate();
  DB::table('bitacoras')->truncate();   
  DB::table('hts')->truncate();   
  DB::table('detalledescuentos')->truncate();  

  DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  return 'Tablas han sido limpiadas!';
}

/****************************************************************************************
* Esta function crea un nuevo periodo contable
*****************************************************************************************/
public static function periodo($todate)
{

  // Inicializa variable para almacenar el total facturado en el mes
  $totalIngresos=0;

  // la fecha de los periodos siempre debera ser el dia primero del mes
  $year= Carbon::parse($todate)->year;
  $month= Carbon::parse($todate)->month;
  $todate= Carbon::createFromDate($year, $month, 1);
  
  // crea nuevo periodo contable
  $periodo= new Pcontable;
  $periodo->periodo = Sity::getMonthName($month).'-'.$year;
  $periodo->fecha= $todate;
  $periodo->cerrado = 0;
  $periodo->save();

  //$this->info('Crea nuevo periodo contable...');        

  // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
  // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
  $secaptos= Secapto::All();
  //dd($secaptos->toArray());

  foreach ($secaptos as $secapto) {
      // Encuentra todas las unidades que pertenecen a la seccion 
      $uns= Un::where('seccione_id', $secapto->seccione_id)
               ->where('activa', 1)->get();
      //dd($uns->toArray());

      // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento
      foreach ($uns as $un) {
           $totalIngresos= $totalIngresos+ floatval($secapto->cuota_mant);
      }
  }
  //dd($totalIngresos);

  // Esta function inicializa en el nuevo periodo todas las cuentas temporales
  // activas presentes en el catalogo de cuentas
  //$this->inicializaCuentasTemp($periodo->id, $month, $year);     
  Sity::inicializaCuentasTemp($periodo->id, $periodo->periodo, $todate);

  // Registra resumen de la facturacion mensual en Ctdiario principal 
  //$this->registraEnCtdiario($totalIngresos, $periodo->id, $month, $year);
  Sity::registraEnCtdiario($totalIngresos, $periodo->id, $periodo->periodo, $todate);
    return 'Nuevo periodo de '.$periodo->periodo.' han sido creado!' ;
}

/****************************************************************************************
* Esta function genera la ordenes de cobro de un mes en particular
*****************************************************************************************/
public static function facturar($fecha)
{
  
  // Inicializa variable para almacenar el total facturado en el mes
  $totalIngresosDia_1=0;        
  $totalIngresosDia_16=0; 
    
  // Construye la fecha de facturacion segun el argumento
  $year=$fecha->year;
  $month= $fecha->month;
  $day= $fecha->day;
  
  if ($day==1) {
    $day='01';
  }
  
  //dd($fecha, $year, $month, $day);
    
  // encuentra el ultimo periodo contable registrado
  $periodo= Pcontable::all()->last(); 
  //dd($periodo);

  // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
  // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
  $secaptos= Secapto::with('seccione')->where('d_registra_cmpc', $day)->get();
  // dd($secaptos->toArray());
  
  foreach ($secaptos as $secapto) {
    // Encuentra el administrador encargado del bloque al cual pertenece la seccion
    $blqadmin= Sity::findBlqadmin($secapto->seccione->bloque_id);
    //dd($blqadmin);

    // Encuentra todas las unidades que pertenecen a la seccion 
    $uns= Un::where('seccione_id', $secapto->seccione_id)
            ->where('activa', 1)->get();
    //dd($uns->toArray());

    // Por cada apartamento que exista registra su cuota de mantenimiento por cobrar en el ctdiario auxiliar
    foreach ($uns as $un) {
      // parametros regulares
      $un_id= $un->id;
      $cuota_mant= floatval($secapto->cuota_mant);
      $descuento= floatval($secapto->descuento);
      $ocobro= $un->codigo.' '.Sity::getMonthName($month).$day.'-'.$year;
      $descuento_siono= 0;
      $pagada= 0;
      
      // antes de crear facturacion para un determinada unidad, se verifica si la misma pago por anticipado 
      // la respectiva orden de cobro
      $desc= Detalledescuento::whereDate('fecha', $fecha->toDateString())
                             ->where('un_id', $un_id)->first();
      //dd($fecha->toDateString());
      
      if ($desc) {
        // si encuentra descuento en la presente orden de cobro, entonces cambia los parametros
        // para que registren el descuento
        $cuota_mant= $desc->importe;
        $descuento= $desc->descuento;
        $descuento_siono= 1;
        $pagada=1;
        
        // registra el descuento como consumido
        $desc->consumido=1;
        $desc->save();            
      
        // registra una disminucion en la cuenta de Pagos anticipados
        Sity::registraEnCuentas($periodo->id, 'menos', 2, 5, $fecha, Catalogo::find(5)->nombre.' '.$un->codigo.' '.$desc->mes_anio, $cuota_mant, $un_id, $desc->pago_id);
        
        // registra un aumento en "Gastos por cuentas incobrables" 
        Sity::registraEnCuentas($periodo->id, 'mas', 6, 13, $fecha, Catalogo::find(13)->nombre.' '.$un->codigo.' '.$desc->mes_anio, $descuento, $un_id, $desc->pago_id);    
        
        // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
        Sity::registraEnCuentas($periodo->id, 'menos', 1, 1, $fecha, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$desc->mes_anio, ($cuota_mant + $descuento), $un_id, $desc->pago_id);
      
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        $diario->fecha   = Carbon::createFromDate($year, $month, 1); 
        $diario->detalle = Catalogo::find(5)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
        $diario->debito  = $cuota_mant;
        $diario->credito = Null;
        $diario->save();
      
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        $diario->detalle = Catalogo::find(13)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
        $diario->debito = $descuento;
        $diario->credito = Null;
        $diario->save();

        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        $diario->detalle = Catalogo::find(1)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
        $diario->debito = Null;
        $diario->credito = $cuota_mant + $descuento;
        $diario->save();        

        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        $diario->detalle = 'Para registrar cobro de couta de mantenimiento con descuento, unidad '.$ocobro;
        $diario->save();

      } 

      // Registra facturacion mensual de la unidad 
      $dato= new Ctdasm;
      $dato->pcontable_id     = $periodo->id;
      $dato->fecha            = $fecha;
      $dato->ocobro           = $ocobro;
      $dato->diafact          = $day;                
      $dato->mes_anio         = Sity::getMonthName($month). '-'.$year;
      $dato->detalle          = 'Cuota de mantenimiento Unidad No ' . $un->id;
      $dato->importe          = $cuota_mant;
      $dato->f_vencimiento    = Sity::fechaLimiteRecargo($secapto->d_registra_cmpc, $fecha->toDateString(), $secapto->m_vence, $secapto->d_vence);
      $dato->recargo          = $secapto->recargo;
      $dato->descuento        = $descuento;             
      $dato->f_descuento      = Carbon::createFromDate($year, $month, $day)->subMonths($secapto->m_descuento);   
      $dato->bloque_id        = $secapto->seccione->bloque_id;
      $dato->seccione_id      = $secapto->seccione_id;
      $dato->blqadmin_id      = $blqadmin;
      $dato->un_id            = $un_id;
      $dato->pagada           = $pagada;
      $dato->descuento_siono  = $descuento_siono;
      $dato->save(); 
      
      // Acumula el total facturado
      $totalIngresosDia_1 = $totalIngresosDia_1+ $cuota_mant;         
      
      // verifica si se puede realizar pagos de cuotas o recargos utilizando solamente el contenido
      // de la cuenta de pagos anticipados de la unidad
      Sity::iniciaPago($un_id, Null, Null, $fecha, $periodo->id, $periodo->periodo); 
    }
  }    
  
  //dd($totalIngresosDia_1);
  
  // si se trata de la facturacion del dia primero se debe incluir el total que debera ingresar
  // mensualmente en concepto de cuotas de mantenimiento para las unidades que generan ordenes de
  // cobro los dias dieciséis de cada mes.
  if ($day==1) {
    // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
    // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
    $secaptos= Secapto::where('d_registra_cmpc', 16)->get();
    //dd($secaptos->toArray());
    
    foreach ($secaptos as $secapto) {
      // Encuentra todas las unidades que pertenecen a la seccion 
      $uns= Un::where('seccione_id', $secapto->seccione_id)
              ->where('activa', 1)->get();
      //dd($uns->toArray());

      // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento
      foreach ($uns as $un) {
           $totalIngresosDia_16= $totalIngresosDia_16+ floatval($secapto->cuota_mant);
      }
    }            
    
    //dd($totalIngresosDia_16);
    
    // Registra facturacion mensual de la unidad en cuenta 'Cuota de mantenimiento por cobrar' 1120.00
    Sity::registraEnCuentas(
            $periodo->id, // periodo                      
            'mas',  // aumenta
            1,      // cuenta id
            1,      // '1120.00',
            Carbon::createFromDate($year, $month, 1),   // fecha
            'Resumen de Cuota de mantenimiento por cobrar '.Sity::getMonthName($month).'-'.$year, // detalle
            ($totalIngresosDia_1+$totalIngresosDia_16) // monto
           );

    // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento' 4120.00
    Sity::registraEnCuentas(
            $periodo->id, // periodo
            'mas',    // aumenta
            4,        // cuenta id
            3,        //'4120.00'
            Carbon::createFromDate($year, $month, 1),   // fecha
            'Resumen de Ingreso por cuota de mantenimiento '.Sity::getMonthName($month).'-'.$year, // detalle
            ($totalIngresosDia_1+$totalIngresosDia_16) // monto
           );
  }  

  //$this->info('Finaliza facturacion para el mes de '.Sity::getMonthName($month).'-'.$year );
  return 'Finaliza facturacion para el mes de '.Sity::getMonthName($month).'-'.$year ;

}

/****************************************************************************************
* Esta function calcula la fecha limite para pagar sin recargo
*****************************************************************************************/
public static function fechaLimiteRecargo($diaFact, $f_ocobro, $m_vence, $d_vence)
{
  
  if ($diaFact==1) {
    
    if ($m_vence == 0) {
      if (Carbon::parse($f_ocobro)->endOfMonth()->day < $d_vence) {
        $f_vence = Carbon::parse($f_ocobro)->endOfMonth();
      
      } else {
        $f_vence = Carbon::parse($f_ocobro)->addDays($d_vence - 1 ); 
      }
    } 

    if ($m_vence > 0) {
      if (Carbon::parse($f_ocobro)->addMonths($m_vence)->endOfMonth()->day < $d_vence) {
        $f_vence = Carbon::parse($f_ocobro)->addMonths($m_vence)->endOfMonth();
      
      } else {
        $f_vence = Carbon::parse($f_ocobro)->addMonths($m_vence)->addDays($d_vence - 1 ); 
      }
    } 

  } elseif ($diaFact==16) {
    
    if ($m_vence == 0) {
      if (Carbon::parse($f_ocobro)->endOfMonth()->day < $d_vence) {
        $f_vence = $f_ocobro->endOfMonth();
      
      } else {
        $f_vence = Carbon::parse($f_ocobro)->addDays($d_vence - 16); 
      }
    } 

    if ($m_vence > 0) {
      if (Carbon::parse($f_ocobro)->addMonths($m_vence)->endOfMonth()->day < $d_vence) {
        $f_vence = Carbon::parse($f_ocobro)->addMonths($m_vence)->endOfMonth();
      
      } else {
        $f_vence = Carbon::parse($f_ocobro)->addMonths($m_vence)->addDays($d_vence - 16 ); 
      }
    }        
  }
  return $f_vence;
}

/****************************************************************************************
* Esta function penaliza en grupo al cierre de periodo
* @param  carbon  $fecha       - fecha de inicio de periodo
*****************************************************************************************/
public static function penalizarTipo1($fecha)
{
  // clona $fecha para mantener su valor original
  $f_limite = clone $fecha;
   
  // encuentra todas las fechas de vencimiento que existen dentro de un periodo
  $vfechas= Ctdasm::whereDate('f_vencimiento','<', $f_limite->endOfMonth()->toDateString())
              ->where('pagada', 0)
              ->where('recargo_siono', 0)
              ->select('f_vencimiento')
              ->orderBy('f_vencimiento')              
              ->distinct()
              ->get();
  //dd($vfechas->toArray());
  
  // si encuentra alguna fecha, quiere decir que hay unidades a penalizar        
  if ($vfechas->count()>0) {
    foreach ($vfechas as $vfecha) {
      
      // determina a que periodo corresponde la fecha de vencimiento 
      $f_vencimiento= Carbon::parse($vfecha->f_vencimiento);
      $month= $f_vencimiento->month;    
      $year= $f_vencimiento->year;    
    
      $pdo= Sity::getMonthName($month).'-'.$year;
      $periodo= Pcontable::where('periodo', $pdo)->first()->id;
      //dd($periodo);

      // encuentra todas aquella unidades que no han sido pagadas y que tienen fecha de pago vencida
      $datos= Ctdasm::where('f_vencimiento', $vfecha->f_vencimiento)
                  ->where('pagada', 0)
                  ->where('recargo_siono', 0)
                  ->get();
      //dd($datos->toArray());

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
                $periodo,
                'mas',
                1,
                2, //'1130.00',
                $f_limite->endOfMonth()->toDateString(),
                'Recargo en cuota de mantenimiento por cobrar unidad '.$dato->ocobro,
                $dato->recargo,
                $dato->un_id
               );

          // registra 'Ingreso por cuota de mantenimiento' 4130.00
          Sity::registraEnCuentas(
                $periodo,
                'mas',
                4,
                4, //'4130.00',
                $f_limite->endOfMonth()->toDateString(),
                '   Ingreso por recargo en cuota de mantenimiento unidad '.$dato->ocobro,
                $dato->recargo,
                $dato->un_id
               );

          // registra resumen de la facturacion mensual en Ctdiario principal 
          if ($i==1) {
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id  = $periodo;
            $dto->fecha   = $f_limite->endOfMonth()->toDateString();
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
    } // end foreach $vfechas
  } // end of if
} // end of function

/****************************************************************************************
* Esta function penaliza individual por fecha de pago
* @param  carbon  $f_pago      - fecha en que se efectuo el pago
* @param  string  $un_id       - unidad que se desea penalizar individualmente
*****************************************************************************************/
public static function penalizarTipo2($f_pago, $un_id)
{
  // clona $fecha para mantener su valor original
  $f_limite = clone $f_pago;

  // penaliza individualmente a una determinada unidad
  $datos= Ctdasm::where('un_id', $un_id)
              ->whereDate('f_vencimiento','<', $f_limite)
              ->where('pagada', 0)
              ->where('recargo_siono', 0)
              ->get();
  //dd($datos->toArray()); 

  $i= 1;   
  
  // inicializa variable para almacenar el total de recargos
  $totalRecargos= 0;       

  if ($datos->count()) {
    foreach ($datos as $dato) {
      // determina a que periodo corresponde la fecha de vencimiento 
      $f_vencimiento= Carbon::parse($dato->f_vencimiento)->addDay();
      $month= $f_vencimiento->month;    
      $year= $f_vencimiento->year;    

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
            $f_vencimiento,
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
            $f_vencimiento,
            '   Ingreso por recargo en cuota de mantenimiento unidad '.$dato->ocobro,
            $dato->recargo,
            $dato->un_id
           );

      // registra resumen de la facturacion mensual en Ctdiario principal 
      if ($i==1) {
        // registra en Ctdiario principal
        $dto = new Ctdiario;
        $dto->pcontable_id  = $periodo;
        $dto->fecha   = $f_vencimiento;
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

/****************************************************************************************
* Esta function penaliza en grupo cada dia desde el cron
* @param  carbon  $today       - fecha del dia de hoy
*****************************************************************************************/
public static function penalizarTipo3($today)
{
  // clona $fecha para mantener su valor original
  $f_limite = clone $today;
   
  // encuentra las fechas de vencimiento del periodo al final del mes
  $vfechas= Ctdasm::whereDate('f_vencimiento','<', $today)
              ->where('pagada', 0)
              ->where('recargo_siono', 0)
              ->select('f_vencimiento')
              ->orderBy('f_vencimiento')              
              ->distinct()
              ->get();
  //dd($vfechas->toArray());
  
  // si encuentra alguna fecha, quiere decir que hay unidades a penalizar        
  if ($vfechas->count()>0) {
    foreach ($vfechas as $vfecha) {
      
      // determina a que periodo corresponde la fecha de vencimiento 
      $f_vencimiento= Carbon::parse($vfecha->f_vencimiento);
      $month= $f_vencimiento->month;    
      $year= $f_vencimiento->year;    
    
      $pdo= Sity::getMonthName($month).'-'.$year;
      $periodo= Pcontable::where('periodo', $pdo)->first()->id;
      //dd($periodo);

      // encuentra todas aquella unidades que no han sido pagadas y que tienen fecha de pago vencida
      $datos= Ctdasm::where('f_vencimiento', $vfecha->f_vencimiento)
                  ->where('pagada', 0)
                  ->where('recargo_siono', 0)
                  ->get();
      //dd($datos->toArray());
      
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
                $periodo,
                'mas',
                1,
                2, //'1130.00',
                $today,
                'Recargo en cuota de mantenimiento por cobrar unidad '.$dato->ocobro,
                $dato->recargo,
                $dato->un_id
               );

          // registra 'Ingreso por cuota de mantenimiento' 4130.00
          Sity::registraEnCuentas(
                $periodo,
                'mas',
                4,
                4, //'4130.00',
                $today,
                '   Ingreso por recargo en cuota de mantenimiento unidad '.$dato->ocobro,
                $dato->recargo,
                $dato->un_id
               );

          // registra resumen de la facturacion mensual en Ctdiario principal 
          if ($i==1) {
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id  = $periodo;
            $dto->fecha   = $today;
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
    } // end foreach $vfechas
  } // end of if
} // end of function


/****************************************************************************************
* Esta function inicializa en el nuevo periodo todas las cuentas temporales
* activas presentes en el catalogo de cuentas
*****************************************************************************************/
public static function inicializaCuentasTemp($pcontable_id, $periodo, $fecha)
{
  // Encuentra todas las cuentas de ingresos activas en el catalogo de cuentas
  $cuentas= Catalogo::where('tipo', 4)
                  ->where('activa', 1)
                  ->get();
  //dd($cuentas->toArray());

  // procesa cada una de las cuentas encontradas
  //$i=1;
  foreach ($cuentas as $cuenta) {
    // agrega un nuevo registro de apertura de periodo en donde se inicializan 
    // todas las cuentas de ingresos en cero
    $dato = new Ctmayore;
    $dato->pcontable_id     = $pcontable_id;
    $dato->tipo             = 4;
    $dato->cuenta           = $cuenta->id;
    $dato->codigo           = $cuenta->codigo;
    $dato->fecha            = $fecha;
    $dato->detalle          = 'Inicializa la cuenta de ingreso '.$cuenta->codigo.' en cero por inicio de periodo contable '.$periodo;
    $dato->debito           = 0;
    $dato->credito          = 0;
    //$dato->saldocta         = 0;
    $dato->save();
  }
  
  // Encuentra todas las cuentas de gastos activas en el catalogo de cuentas
  $cuentas= Catalogo::where('tipo', 6)
                  ->where('activa', 1)
                  ->get();
  // dd($cuentas->toArray());

  // procesa cada una de las cuentas encontradas
  foreach ($cuentas as $cuenta) {
    // agrega un nuevo registro de apertura de periodo en donde se inicializan 
    // todas las cuentas de gastos en cero
    $dato = new Ctmayore;
    $dato->pcontable_id     = $pcontable_id;
    $dato->tipo             = 6;
    $dato->cuenta           = $cuenta->id;
    $dato->codigo           = $cuenta->codigo;
    $dato->fecha            = $fecha;
    $dato->detalle          = 'Inicializa la cuenta de gasto '.$cuenta->codigo.' en cero por inicio de periodo contable '.$periodo;
    $dato->debito           = 0;
    $dato->credito          = 0;
    $dato->save();
  }
  
  //$this->info('Inicializa cuenta temporales para inicio de nuevo periodo contable...');
}

/****************************************************************************************
* Esta function cierra todas las cuentas temporales que tuvieron activas en el periodo
* contable a cerrar
*****************************************************************************************/
public static function cierraCuentasTemp($pcontable_id, $fecha)
{

  // Antes de comenzar a cerrar la cuenta temporales, se calcula la Utilidad neta del periodo a cerrarse
  $utilidad= Sity::getUtilidadNeta($pcontable_id); 

  // Encuentra todas las cuentas de ingresos activas en periodo contable a cerrar
  $cuentas= Ctmayore::where('tipo', 4)
                  ->where('pcontable_id', $pcontable_id)
                  ->get();
  
  $cuentas= $cuentas->unique('cuenta');
  //dd($cuentas->toArray());

  // procesa cada una de las cuentas encontradas
  $i=1;
  foreach ($cuentas as $cuenta) {
    // almacena el saldo de la cuenta antes de cerrarla
    $saldoIngresos= Sity::getSaldoCuenta($cuenta->cuenta, $pcontable_id);

    // agrega un nuevo registro de cierre de cuenta
    $dato = new Ctmayore;
    $dato->pcontable_id     = $pcontable_id;
    $dato->tipo             = 4; 
    $dato->cuenta           = $cuenta->cuenta;
    $dato->codigo           = $cuenta->codigo;
    $dato->fecha            = $fecha->endOfMonth();
    $dato->detalle          = Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
    $dato->debito           = $saldoIngresos;
    $dato->credito          = 0;
    $dato->save();
    
    if ($saldoIngresos>0) {      
      if ($i==1) {
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $pcontable_id;
        $dato->fecha         = $fecha;
        $dato->detalle = Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
        $dato->debito  = $saldoIngresos;
        $dato->save();  
      
      } else {
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $pcontable_id;
        $dato->detalle = Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
        $dato->debito  = $saldoIngresos;
        $dato->save();  
      }
      $i++;
    }    
  }

  // Encuentra todas las cuentas de ingresos activas en periodo contable a cerrar
  $cuentas= Ctmayore::where('tipo', 6)
                  ->where('pcontable_id', $pcontable_id)
                  ->get();
  $cuentas = $cuentas->unique('cuenta');
  //dd($cuentas->toArray());

  // procesa cada una de las cuentas encontradas
  foreach ($cuentas as $cuenta) {
    // almacena el saldo de la cuenta antes de cerrarla
    $saldoGastos= Sity::getSaldoCuenta($cuenta->cuenta, $pcontable_id);

    // agrega un nuevo registro de cierre de cuenta
    $dato = new Ctmayore;
    $dato->pcontable_id     = $pcontable_id;
    $dato->tipo             = 6;
    $dato->cuenta           = $cuenta->cuenta;
    $dato->codigo           = $cuenta->codigo;
    $dato->fecha            = $fecha;
    $dato->detalle          = '   '.Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
    $dato->debito           = 0;
    $dato->credito          = $saldoGastos;
    $dato->save();
    
    if ($saldoGastos>0) {      
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $pcontable_id;
        $dato->detalle = '   '.Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
        $dato->credito = $saldoGastos;
        $dato->save();  
      }
  }

  // registra la utilidad del periodo
  if ($utilidad>0 || $utilidad==0) {
    // En Ctmayores se registra un aumento en utilidad neta 
    Sity::registraEnCuentas($pcontable_id, 'mas', 3, 7, $fecha, 'Se registra aumento en la utilidad del periodo', $utilidad, Null, Null);
  
    // registra la utilidad en el diario del periodo posterior
    $data = new Ctdiario;
    $data->pcontable_id     = $pcontable_id;
    $data->detalle          = '   Utilidad neta';
    $data->credito          = $utilidad;
    $data->save();   

  } elseif ($utilidad<0) {
    // En Ctmayores se registra una disminucion en utilidad neta 
    Sity::registraEnCuentas($pcontable_id, 'menos', 3, 7, $fecha, 'Se registra una perdida en el periodo', $utilidad, Null, Null);
  
    // registra la utilidad en el diario del periodo posterior
    $data = new Ctdiario;
    $data->pcontable_id     = $pcontable_id;
    $data->detalle          = '   Perdida neta';
    $data->debito           = $utilidad;
    $data->save(); 
  }

  // registra la utilidad en el diario del periodo posterior
  $data = new Ctdiario;
  $data->pcontable_id     = $pcontable_id;
  $data->detalle          = 'Para cerrar cuentas temporales y registrar utilidad neta de '.Pcontable::find($pcontable_id)->periodo;
  $data->save(); 
}


/****************************************************************************************
 * Esta function registra en Ctdiario principal el resumen de la facturacion del mes
 *****************************************************************************************/
public static function registraEnCtdiario($totalIngresos, $periodo_id, $periodo, $fecha)
{
       
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->fecha         = $fecha;
    $dato->detalle       = 'Cuota de mantenimiento por cobrar';
    $dato->debito        = $totalIngresos;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id = $periodo_id;
    $dato->detalle = '   Ingresos por cuota de mantenimiento';
    $dato->credito = $totalIngresos;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id = $periodo_id;
    $dato->detalle = 'Para registrar resumen de ingresos por cuotas de mantenimiento de '.$periodo;
    $dato->save(); 

    //$this->info('Finaliza registro de resumen en el diario');
}

/****************************************************************************************
 * Esta function registra en Ctdiario principal el resumen de la facturacion del mes
 *****************************************************************************************/
public static function registraRecargosEnCtdiario($recargo, $periodo_id, $ocobro)
{
       
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->fecha   = Carbon::today();
    $dato->detalle = 'Recargo por cobrar en cuota de mantenimiento';
    $dato->debito  = $recargo;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->detalle = '   Ingresos por cuota de mantenimiento';
    $dato->credito = $recargo;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->detalle = 'Para registrar recargo en cuotas de mantenimiento de la unidad '.$ocobro;
    $dato->save(); 
}

//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
// fin de funciones para hacer pruebas de facturacion
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$



/***********************************************************************************
* Almacena datos del periodo antes de cerrarlo y las almacena en la tabla Hts
* hoja de trabajo historica
************************************************************************************/ 
public static function migraDatosHts($pcontable_id) {

    // encuentra la data necesaria para confeccionar la hoja de trabajo historica
    $datos= Sity::getDataParaHojaDeTrabajo($pcontable_id);
    //dd($datos); 
    
    // procesa cada una de las cuentas encontradas
    foreach ($datos as $dato) {        
        $dto = new Ht;
        $dto->cuenta       = $dato['cuenta'];
        $dto->tipo         = $dato['tipo'];
        $dto->codigo       = $dato['codigo'];
        $dto->nombre       = $dato['cta_nombre'];
        $dto->clase        = $dato['clase'];
        $dto->bp_debito    = $dato['saldo_debito'];
        $dto->bp_credito   = $dato['saldo_credito'];
        $dto->aj_debito    = $dato['saldoAjuste_debito'];
        $dto->aj_credito   = $dato['saldoAjuste_credito'];            
        $dto->ba_debito    = $dato['saldoAjustado_debito'];
        $dto->ba_credito   = $dato['saldoAjustado_credito'];
        
        if ($dato['tipo']==1 || $dato['tipo']==2 || $dato['tipo']==3) {
            $dto->bg_debito    = $dato['saldoAjustado_debito'];
            $dto->bg_credito   = $dato['saldoAjustado_credito'];

        } elseif ($dato['tipo']==4 || $dato['tipo']==6) {
            $dto->er_debito    = $dato['saldoAjustado_debito'];
            $dto->er_credito   = $dato['saldoAjustado_credito'];
        }
      
        $dto->pcontable_id = $dato['periodo'];
        $dto->save(); 
    }
     //dd($dato);
}

/***********************************************************************************
* Al momento de cerrar un determinado periodo contable, el sistema migra los datos
* de ctmayores a la tabla de datos historicos ctmayorehis y posteriormente borra
* datos migrados de la tabla ctmayores
************************************************************************************/ 
public static function migraDatosCtmayorehis($pcontable_id) {
  $datos= Ctmayore::where('pcontable_id', $pcontable_id)->get();
  foreach ($datos as $dato) {
      $data= new Ctmayorehi;
      $data->id               = $dato->id;
      $data->pcontable_id     = $dato->pcontable_id;
      $data->tipo             = $dato->tipo;
      $data->cuenta           = $dato->cuenta;
      $data->codigo           = $dato->codigo;
      $data->fecha            = $dato->fecha;
      $data->detalle          = $dato->detalle;
      $data->debito           = $dato->debito;
      $data->credito          = $dato->credito;
      //$data->saldocta         = $dato->saldocta;
      $data->un_id            = $dato->un_id;
      $data->org_id           = $dato->org_id; 
      $data->save();

      // borra todos los datos del periodo de la tabla ctmayores
      Ctmayore::destroy($dato->id);
  }
}

/***********************************************************************************
* Al momento de cerrar un determinado periodo contable, el sistema migra los datos
* de ctdiarios a la tabla de datos historicos ctdiariohis y posteriormente los borra
* los datos migrados de la tabla ctdiarios
************************************************************************************/ 
public static function migraDatosCtdiariohis($pcontable_id) {
  $datos= Ctdiario::where('pcontable_id', $pcontable_id)->get();
  foreach ($datos as $dato) {

    $data = new Ctdiariohi;
    $data->id               = $dato->id;     
    $data->pcontable_id     = $dato->pcontable_id;
    $data->fecha            = $dato->fecha;
    $data->detalle          = $dato->detalle;
    $data->debito           = $dato->debito;
    $data->credito          = $dato->credito;
    $data->save(); 

    // borra todos los datos del periodo de la tabla ctdiarios
    Ctdiario::destroy($dato->id);
  }
}

  /***********************************************************************************
  * Proceso de contabilizar los pagos recibidos
  ************************************************************************************/ 
  public static function iniciaPago($un_id, $montoRecibido, $pago_id, $f_pago, $periodo, $pdo) {
    //dd($montoRecibido);
    //$montoRecibido = round(floatval($montoRecibido),2);
   
    // procesa el pago recibido
    Sity::procesaPago($periodo, $un_id, $montoRecibido, $pago_id, $f_pago);
 
    // llama al proceso que pasa los registro del pago del ctmayores al ctdiarios 
    if ($pago_id && $montoRecibido) {
      // si $pago_id y $montoRecibido existen, quiere decir que se trata de un pago normal      
      Sity::registaEnDiario($pago_id);
    } 
 
    // procede a notificar al propietario la generacion de una nueva order de cobro
    
    // encuentra los datos para generar el estado de cuentas de un determinada unidad
    //$datos= Sity::getdataEcuenta($un_id);
    //dd($datos['imps']->toArray());    
    
    $props=Prop::where('un_id', $un_id)
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
  
  /***********************************************************************************
  * Proceso de recoger la data de un estado de cuentas
  ************************************************************************************/ 
  public static function getdataEcuenta($un_id) {
    $imps = Ctdasm::where('un_id', $un_id)
                      ->where('pagada', 0)
                      ->select('id','mes_anio','f_vencimiento','importe')
                      ->orderBy('fecha')
                      ->get();    

    $recs = Ctdasm::where('un_id', $un_id)
                      ->where('recargo_siono', 1) 
                      ->where('recargo_pagado', 0)
                      ->select('id','mes_anio','recargo_siono','recargo_pagado','recargo')
                      ->orderBy('fecha')
                      ->get();

    // inicializa los contadores
    $total_importe=0;
    $total_recargo=0;

    foreach ($imps as $imp) {
      $imp->f_vencimiento= Date::parse($imp->f_vencimiento)->toFormattedDateString();

      // Acumula el total de importe a pagar
      if ($imp->pagada==0) {
        $total_importe  = $total_importe + $imp->importe;  
      }
    }       

    foreach ($recs as $rec) {
      // Acumula el total de recargos a pagar
      if ($rec->recargo_siono==1 && $rec->recargo_pagado==0) {
        $total_recargo  = $total_recargo + $rec->recargo;  
      }      
    }     

    // Obtiene todos los propietarios de una determinada unidad que sean encardados.  
    $prop = Prop::where('un_id', $un_id)
                  ->where('encargado', '1')
                  ->with('user')
                  ->first();
    // dd($prop); 

    // Encuentra los datos de la unidad
    $un = Un::find($un_id);
    // dd($un->toArray());

    // Encuentra los datos de la sección a la cual pertenece la unidad
    $seccion = Seccione::find($un->seccione_id);
    // dd($seccion->toArray());
    
    // Encuentra los datos del Ph al que pertenece la unidad
    $ph = Ph::find($seccion->ph_id);
    // dd($ph->toArray()); 
    
    // Encuentra saldo pagados por anticipado
    $pagos_anticipados = Sity::getSaldoCtaPagosAnticipados($un_id, Null);
    $total=number_format(($total_importe + $total_recargo), 2);
    
    if ($total==0) {
      $total_adeudado=(number_format(0, 2));
    }
    else {
      $total_adeudado=abs(number_format($total-$pagos_anticipados, 2));      
    }

    // Prepara datos del encabezado del Estado de cuenta
   
    $data = [
      'Titulo'    => 'Bienvenido al ctmaster.net',
      'Contenido'   => 'Contenido del email',
      
      'phlogo'    => $ph->logo,
      'phnombre'    => $ph->nombre,
      'phcalle'   => $ph->calle,
      'phlote'    => $ph->lote,
      'phdistrito'  => $ph->distrito,
      'phprovincia' => $ph->provincia,
      'phtelefono'  => $ph->telefono,
      'phemail'   => $ph->email,          
      
      'propnombre'    => $prop->user->nombre_completo,          
      'propprovincia'   => $prop->user->provincia,
      'propdistrito'    => $prop->user->distrito,
      'propcorregimiento' => $prop->user->corregimiento,
      
      'un_id'      => $un->id, 
      'codigo'      => $un->codigofull, 
      'fecha'       => Date::today()->format('l\, j \d\e F Y'),
      'total'  => 'B/. ' . $total,
      'pagos_anticipados'  => 'B/. ' . number_format($pagos_anticipados, 2),
      'total_adeudado'  => 'B/. ' . number_format($total_adeudado, 2),
    ];
       
    //dd($imps->toArray(),$recs->toArray(),$total_importe,$total_recargo);
    return array('imps'=>$imps,'recs'=>$recs,'data'=>$data);  
  }

  /***********************************************************************************
  * Registra en diario el resumen de las transacciones generadas producto del pago recibido
  ************************************************************************************/ 
  public static function registaEnDiario($pago_id) {
    // encuentra todos los periodos contables sin cerrar
    $periodos= Pcontable::where('cerrado', 0)->orderBy('id')->get();       

    foreach ($periodos as $periodo) {    
      // encuentra todos los registros en ctmayores que pertenecen a un determinado pago
      $cuentas= Ctmayore::where('pago_id',$pago_id)
                        ->where('pcontable_id',$periodo->id)
                        ->where('anula',Null)
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
            $diario->pcontable_id  = $periodo->id;
            $diario->fecha   = $cuenta->fecha; 
            $diario->detalle = $cuenta->detalle;
            $diario->debito  = $cuenta->debito;
            $diario->credito = $cuenta->credito;
            $diario->save();
          
          } else {
          // registra en el diario
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = $cuenta->detalle;
            $diario->debito = $cuenta->debito;
            $diario->credito = $cuenta->credito;
            $diario->save();
          }
          $i=1;
        }

        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        $diario->detalle = 'Para registrar transacciones producto del Pago No. '.$pago_id;
        $diario->save();
      } // endif
    } // end foreach
  } // fin de function     










} //fin de Sity