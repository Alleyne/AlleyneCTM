<?php namespace App\library;

use Carbon\Carbon;
use App\library\Sity;

use App\Pcontable;
use App\Secapto;
use App\Catalogo;
use App\Un;
use App\Ctdasm;
use App\Blqadmin;
use App\Detalledescuento;

class Fact {

  /** 
  *==================================================================================================
  * ejecuta proceso de facturacion mensual para todas las secciones cuya ocobro se genera
  * los dias primero o dieciseis de cada mes
  * @param  date/Carbon $date yyyy/mm/01 o yyyy/mm/16
  * @return void
  **************************************************************************************************/
  public static function facturar($fecha) {
    
    // Inicializa variable para almacenar el total facturado en el mes
    $totalIngresosDia_1= 0;        
    $totalIngresosDia_16= 0; 
    
    // inicializa los contadores en cero
    $totalExtraordinariaDia_1= 0;
    $totalExtraordinariaDia_16= 0;

    // Construye la fecha de facturacion segun el argumento
    $year=$fecha->year;
    $month= $fecha->month;
    $day= $fecha->day;
    
    if ($day== 1) {
      $day='01';
    }
      
    // encuentra el ultimo periodo contable registrado
    $periodo= Pcontable::all()->last(); 
    //dd($periodo);

    // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
    // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
    $secaptos= Secapto::with('seccione')->where('d_registra_cmpc', $day)->get();
    // dd($secaptos->toArray());
    
    foreach ($secaptos as $secapto) {
      $extra_siono = 0;
      $extra = Null;

      // Encuentra el administrador encargado del bloque al cual pertenece la seccion
      $blqadmin= Sity::findBlqadmin($secapto->seccione->bloque_id);
      // dd($blqadmin);

      // verifica si hay cuotas extraordinarias que aplicar a la seccion
      if ($secapto->f_iniciaextra) {
        $f_inicio = Carbon::parse($secapto->f_iniciaextra); // fecha en que inicia el cobro de la cuota extraordinaria
        $f_final = Carbon::parse($secapto->f_iniciaextra)->addMonths($secapto->extra_meses); // fecha en que termina el cobro de la cuota extraordinaria
        
        if ($fecha->between($f_inicio, $f_final)) {
          // si la fecha del periodo en estudio esta entre la fecha de inicio y la fecha final,
          // quiere decir que hay que aplicar el cobro de la cuota extraordinaria a todas las unidades de la seccion en estudio
          $extra_siono= 1;
          $extra = $secapto->extra;
        }
      }
      
      // Encuentra todas las unidades que pertenecen a la seccion 
      $uns = Un::where('seccione_id', $secapto->seccione_id)
              ->where('activa', 1)->get();
      // dd($uns->toArray());

      // Por cada apartamento que exista registra su cuota de mantenimiento por cobrar en el ctdiario auxiliar
      foreach ($uns as $un) {
        // parametros regulares
        $un_id= $un->id;
        $cuota_mant= floatval($secapto->cuota_mant);
        $descuento= floatval($secapto->descuento);
        $ocobro= $un->codigo.' Oc '.Sity::getMonthName($month).' '.$day.'-'.$year;
        $descuento_siono= 0;
        $pagada= 0;

        // antes de crear facturacion para un determinada unidad, se verifica si la misma se pagó por anticipado. 
        // Si la orden de cobro ha sido pagada por anticipada, procede a contabilizarla
        $desc = Detalledescuento::where('fecha', $fecha->toDateString())
                               ->where('un_id', $un_id)->first();

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
          
          //dd($fecha, $un, $periodo_id, $desc);        
          // contabiliza el pago anticipado en libros
          Desc::contabilizaPagoConDescuento($fecha, $un, $periodo->id, $desc);
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
        $dato->f_vencimiento    = self::fechaLimiteRecargo($secapto->d_registra_cmpc, $fecha->toDateString(), $secapto->m_vence, $secapto->d_vence);
        $dato->recargo          = $secapto->recargo;
        $dato->descuento        = $descuento;             
        $dato->f_descuento      = Carbon::createFromDate($year, $month, $day)->subMonths($secapto->m_descuento);   
        $dato->bloque_id        = $secapto->seccione->bloque_id;
        $dato->seccione_id      = $secapto->seccione_id;
        $dato->blqadmin_id      = $blqadmin;
        $dato->un_id            = $un_id;
        $dato->pagada           = $pagada;
        $dato->descuento_siono  = $descuento_siono;
        $dato->extra_siono      = $extra_siono;
        $dato->extra            = $extra;
        $dato->save(); 

      } // end foreach
    
    } // end foreach secapto

    return 'Finaliza facturacion para el mes de '.Sity::getMonthName($month).'-'.$year ;
  } // end function

  /** 
  *==================================================================================================
  * Esta function calcula la fecha limite para pagar sin recargo
  * @param integer  $diaFact    1
  * @param string   $f_ocobro   "2016-01-01"
  * @param integer  $m_vence    0
  * @param integer  $d_vence    17
  * @return void
  **************************************************************************************************/
  public static function fechaLimiteRecargo($diaFact, $f_ocobro, $m_vence, $d_vence) {
    //dd($diaFact, $f_ocobro, $m_vence, $d_vence);
    
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

} //fin de Class Fact