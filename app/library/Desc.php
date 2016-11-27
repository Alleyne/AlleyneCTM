<?php namespace App\library;

use Carbon\Carbon;
use App\library\Sity;
use App\library\Pant;

use App\Pcontable;
use App\Secapto;
use App\Catalogo;
use App\Un;
use App\Ctdasm;
use App\Detalledescuento;
use App\Detallepago;
use App\Ctdiario;

class Desc {

  /** 
  *==================================================================================================
  * Verifica si se trata de un pago anticipado con el proposito de obtener descuento
  * @param  string  $un_id
  * @param  integer $montoRecibido
  * @param  integer $pago_id  
  * @param  integer $periodo  
  * @param  string  $f_pago
  * @return void
  **************************************************************************************************/
  public static function verificaDescuento($un_id, $montoRecibido, $pago_id, $periodo, $f_pago) {
    //dd($un_id, $montoRecibido, $pago_id, $periodo, $f_pago);
    
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

    // verifica si tiene algun couta extraordinaria por pagar
    $dato = Ctdasm::where('pcontable_id','<=', $periodo)
                   ->where('un_id', $un_id)
                   ->where('extra_siono', 1)
                   ->where('extra_pagada', 0)
                   ->first();
    //dd($dato->toArray());

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
    $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
    //dd($saldocpa);

    // calcula la cantidad de meses que se podrian pagar con el $montoRecibido
    $meses= intdiv(($montoRecibido + $saldocpa), ($seccion->cuota_mant-$seccion->descuento));
    //dd($meses, $montoRecibido, ($seccion->cuota_mant - $seccion->descuento));
    
    // determina si se aplica descuento de acuerdo con la normativa del ph
    if ($meses >= $seccion->m_descuento) {
      // aplica descuento
      
      // calcula el total de dinero a contabilizar 
      $totalContabilizar= $seccion->m_descuento * ($seccion->cuota_mant - $seccion->descuento);
      
      // registra un aumento en la cuenta Banco 
      //Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, Catalogo::find(8)->nombre.' '.$un->codigo, $totalContabilizar, $un_id, $pago_id);    
      
      // registra un aumento en la cuenta de anticipos comprometidos
      //Sity::registraEnCuentas($periodo, 'mas', 2, 14, $f_pago, Catalogo::find(14)->nombre.' '.$un->codigo, $totalContabilizar, $un_id, $pago_id);
      
      $totalUtilizado = 0;    

      // registra en la tabla detalledescuentos el desglose de los meses a los que se les aplicara el descuento
      for ($x = 1; $x <= $meses; $x++) {
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

          // registra un aumento en la cuenta Banco 
          Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, 'Cuota regular de mantenimiento con descuento '.$un->codigo.' '.$mes_anio.' por adelantado', ($seccion->cuota_mant - $seccion->descuento), $un_id, $pago_id);    
          
          // registra un aumento en la cuenta de anticipos comprometidos
          Sity::registraEnCuentas($periodo, 'mas', 2, 14, $f_pago, Catalogo::find(14)->nombre.' '.$un->codigo.' '.$mes_anio, ($seccion->cuota_mant - $seccion->descuento), $un_id, $pago_id);

          // Registra en Detallepago para generar un renglon en el recibo
          Npago::registrAdetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Npago::getLastNoDetallepago($pago_id), 1);
        
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

          // calcula el total a descontar de la cuenta de pagos anticipados necesarios para completar el pago del mes con descuento
          $totalDescontarPa = (($seccion->cuota_mant - $seccion->descuento) - $montoRecibido);
      
          // registra un aumento en la cuenta Banco 
          Sity::registraEnCuentas($periodo, 'mas', 1, 8, $f_pago, 'Cuota regular de mantenimiento con descuento '.$un->codigo.' '.$mes_anio.' por adelantado', $montoRecibido, $un_id, $pago_id);    

          // registra una disminucion en la cuenta de Pagos anticipados 
          Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' '.$un->codigo, $totalDescontarPa, $un_id, $pago_id);    
          
          // registra un aumento en la cuenta de anticipos comprometidos
          Sity::registraEnCuentas($periodo, 'mas', 2, 14, $f_pago, Catalogo::find(14)->nombre.' '.$un->codigo.' '.$mes_anio, ($seccion->cuota_mant - $seccion->descuento), $un_id, $pago_id);
          
          // Registra en Detallepago para generar un renglon en el recibo
          Npago::registrAdetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Npago::getLastNoDetallepago($pago_id), 1);

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

          // calcula el total a descontar de la cuenta de pagos anticipados necesarios para completar el pago del mes con descuento
          $totalDescontarPa = ($seccion->cuota_mant - $seccion->descuento);
   
          // registra una disminucion en la cuenta de Pagos anticipados 
          Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, Catalogo::find(5)->nombre.' '.$un->codigo, $totalDescontarPa, $un_id, $pago_id);    
          
          // registra un aumento en la cuenta de anticipos comprometidos
          Sity::registraEnCuentas($periodo, 'mas', 2, 14, $f_pago, Catalogo::find(14)->nombre.' '.$un->codigo.' '.$mes_anio, ($seccion->cuota_mant - $seccion->descuento), $un_id, $pago_id);

          // Registra en Detallepago para generar un renglon en el recibo
          Npago::registrAdetallepago($periodo, $un->codigo.' '.$mes_anio, 'Paga cuota de mantenimiento de '.$mes_anio.' por anticipado', $dto->id, ($seccion->cuota_mant- $seccion->descuento), $un_id, $pago_id, Npago::getLastNoDetallepago($pago_id), 1);

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



      // registra un aumento en la cuenta de anticipos comprometidos
      //Sity::registraEnCuentas($periodo, 'mas', 2, 14, $f_pago, Catalogo::find(14)->nombre.' '.$un->codigo, $totalContabilizar, $un_id, $pago_id);
      
    } // end first if
    
    return $montoRecibido;

  } // end function


  /** 
  *==================================================================================================
  * Contabiliza en libros el pago anticipado con descuento de una orden de cobro 
  * @param  date      $fecha        "date": "2016-02-16 19:11:58.000000"
  * @param  object    $un           Un {#619 ▶}
  * @param  integer   $periodo_id   2
  * @param  object    $desc         Detalledescuento {#620 ▶}         
  * @return void
  **************************************************************************************************/
  public static function contabilizaPagoConDescuento($fecha, $un, $periodo_id, $desc) {
    //dd($fecha, $un, $periodo_id, $desc);
    
    // registra una disminucion en la cuenta de Pagos anticipados
    Sity::registraEnCuentas($periodo_id, 'menos', 2, 14, $fecha, Catalogo::find(14)->nombre.' '.$un->codigo.' '.$desc->mes_anio, $desc->importe, $un->id, $desc->pago_id);
    
    // registra un aumento en "Gastos por cuentas incobrables" 
    Sity::registraEnCuentas($periodo_id, 'mas', 6, 13, $fecha, Catalogo::find(13)->nombre.' '.$un->codigo.' '.$desc->mes_anio, $desc->descuento, $un->id, $desc->pago_id);    
    
    // registra un aumento en la cuenta 1120.00 "Cuentas por cobrar por cuota de mantenimiento" 
    Sity::registraEnCuentas($periodo_id, 'menos', 1, 1, $fecha, Catalogo::find(1)->nombre.' '.$un->codigo.' '.$desc->mes_anio, ($desc->importe + $desc->descuento), $un->id, $desc->pago_id);

    // registra en el diario
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo_id;
    $diario->fecha   = $fecha; 
    $diario->detalle = Catalogo::find(14)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
    $diario->debito  = $desc->importe;
    $diario->credito = Null;
    $diario->save();

    // registra en el diario
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo_id;
    $diario->detalle = Catalogo::find(13)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
    $diario->debito = $desc->descuento;
    $diario->credito = Null;
    $diario->save();

    // registra en el diario
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo_id;
    $diario->detalle = Catalogo::find(1)->nombre.' '.$un->codigo.' '.$desc->mes_anio;
    $diario->debito = Null;
    $diario->credito = $desc->importe + $desc->descuento;
    $diario->save();        

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo_id;
    $diario->detalle = 'Para registrar cobro de cuota de mantenimiento con descuento, unidad '.$un->codigo.' '.$desc->mes_anio;
    $diario->save();
  }



} //fin de Class Npdo