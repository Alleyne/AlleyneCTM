<?php namespace App\library;

use Carbon\Carbon;
use App\library\Sity;

use App\Ctdiario;
use App\Ctmayore;
use App\Pcontable;
use App\Secapto;
use App\Catalogo;
use App\Un;
use App\Ctdasm;
use App\Blqadmin;
use App\Detalledescuento;

class Npdo {

  /** 
  *=============================================================================================
  * Esta function crea un nuevo periodo contable
  * @param  date/Carbon $fecha +"date": "2016-04-01 00:00:00.000000"
  * @return void
  *********************************************************************************************/
  public static function periodo($fecha) {
    //dd($fecha);

    $year= $fecha->year;
    $month= $fecha->month;
    
    // crea nuevo periodo contable
    $periodo= new Pcontable;
    $periodo->periodo = Sity::getMonthName($month).'-'.$year;
    $periodo->fecha= $fecha;
    $periodo->cerrado = 0;
    $periodo->save();      

    // inicializa en el libro mayor todas las cuentas temporales activas presentes en el
    // catalogo de cuentas, no registra en el diario principal      
    Npdo::inicializaCuentasTemp($periodo->id, $periodo->periodo, $fecha);
    
    // calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento regulares
    // para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes
    Npdo::ingresoEsperadoCuotaRegular(1, $fecha, $periodo->id, $periodo->periodo); 
    Npdo::ingresoEsperadoCuotaRegular(16, $fecha, $periodo->id, $periodo->periodo);
    
    // calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento extraordinarias
    // para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes
    Npdo::ingresoEsperadoCuotaExtraordinaria(1, $fecha, $periodo->id, $periodo->periodo);
    Npdo::ingresoEsperadoCuotaExtraordinaria(16, $fecha, $periodo->id, $periodo->periodo);
    
    return 'Nuevo periodo de '.$periodo->periodo.' han sido creado!' ;
  }

  /** 
  *=============================================================================================
  * Esta function inicializa en el nuevo periodo todas las cuentas temporales
  * activas presentes en el catalogo de cuentas
  * @param  integer $pcontable_id
  * @param  string  $periodo
  * @param  date/Carbon  $date
  * @return void
  *===========================================================================================*/
  public static function inicializaCuentasTemp($pcontable_id, $periodo, $fecha) {
    // Encuentra todas las cuentas de ingresos activas en el catalogo de cuentas
    $cuentas= Catalogo::where('tipo', 4)
                    ->where('activa', 1)
                    ->get();
    //dd($cuentas->toArray());

    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // agrega un nuevo registro de apertura de periodo en donde se inicializan todas las cuentas de ingresos en cero
      $dato = new Ctmayore;
      $dato->pcontable_id  = $pcontable_id;
      $dato->tipo          = 4;
      $dato->cuenta        = $cuenta->id;
      $dato->codigo        = $cuenta->codigo;
      $dato->fecha         = $fecha;
      $dato->detalle       = 'Inicializa cuenta '.$cuenta->codigo.' en cero por inicio de periodo contable '.$periodo;
      $dato->debito        = 0;
      $dato->credito       = 0;
      $dato->save();
    }
    
    // Encuentra todas las cuentas de gastos activas en el catalogo de cuentas
    $cuentas= Catalogo::where('tipo', 6)
                    ->where('activa', 1)
                    ->get();
    // dd($cuentas->toArray());

    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // agrega un nuevo registro de apertura de periodo en donde se inicializan todas las cuentas de gastos en cero
      $dato = new Ctmayore;
      $dato->pcontable_id = $pcontable_id;
      $dato->tipo         = 6;
      $dato->cuenta       = $cuenta->id;
      $dato->codigo       = $cuenta->codigo;
      $dato->fecha        = $fecha;
      $dato->detalle      = 'Inicializa cuenta '.$cuenta->codigo.' en cero por inicio de periodo contable '.$periodo;
      $dato->debito       = 0;
      $dato->credito      = 0;
      $dato->save();
    }
  }

  /** 
  *=============================================================================================
  * calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento regulares
  * para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes
  * @param  integer     $dia
  * @param  date/Carbon $date
  * @param  integer     $periodo_id
  * @param  string     $periodo
  * @return void
  **********************************************************************************************/
  public static function ingresoEsperadoCuotaRegular($dia, $fecha, $periodo_id, $periodo) {
    //dd($dia, $fecha, $periodo_id, $periodo);
    
    $totalIngresos=0;

    // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
    // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
    $secaptos= Secapto::where('d_registra_cmpc', $dia)->get();
    //dd($secaptos->toArray());

    if (!$secaptos->isEmpty()) {
      foreach ($secaptos as $secapto) {
        // Encuentra todas las unidades que pertenecen a la seccion 
        $uns= Un::where('seccione_id', $secapto->seccione_id)
                ->where('activa', 1)->get();
        //dd($uns->toArray());

        // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento
        foreach ($uns as $un) {
          $totalIngresos = $totalIngresos + floatval($secapto->cuota_mant);
        }
      }
      
      // si encuentra total de cuotas regulares registra en libros
      if ($totalIngresos > 0) {

        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo_id;
        $dato->fecha         = $fecha;
        $dato->detalle       = 'Cuota de mantenimiento regular por cobrar';
        $dato->debito        = $totalIngresos;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo_id;
        $dato->detalle = '   Ingresos por cuota de mantenimiento regular';
        $dato->credito = $totalIngresos;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo_id;
        $dato->detalle = 'Para registrar resumen de ingresos por cuotas de mantenimiento regular de '.$periodo.'- OC dia '.$dia;
        $dato->save(); 
      
        // Registra facturacion mensual de la unidad en cuenta 'Cuota de mantenimiento por cobrar' 1120.00
        Sity::registraEnCuentas(
                $periodo_id, // periodo                      
                'mas',  // aumenta
                1,      // cuenta id
                1,      // '1120.00',
                $fecha,   // fecha
                'Resumen de Cuota de mantenimiento regular por cobrar '.$periodo.'- OC dia '.$dia, // detalle
                $totalIngresos // monto
              );

        // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento' 4120.00
        Sity::registraEnCuentas(
                $periodo_id, // periodo
                'mas',    // aumenta
                4,        // cuenta id
                3,        //'4120.00'
                $fecha,   // fecha
                'Resumen de Ingreso por cuota de mantenimiento regular'.$periodo.'- OC dia '.$dia, // detalle
                $totalIngresos // monto
              );
      } // end if 2
    } // end if 1
  } // end function

  /** 
  *==================================================================================================
  * calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento extraordinarias
  * para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes
  * @param  integer     $dia
  * @param  date/Carbon $fecha
  * @param  integer     $periodo_id
  * @param  string      $periodo
  * @return void
  **************************************************************************************************/
  public static function ingresoEsperadoCuotaExtraordinaria($dia, $fecha, $periodo_id, $periodo) {
    //dd($dia, $fecha, $periodo_id, $periodo);
    
    $totalExtraordinaria=0;
    
    // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
    // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
    $secaptos= Secapto::where('d_registra_cmpc', $dia)->get();
    //dd($secaptos->toArray());
    
    if (! $secaptos->isEmpty()) {
      foreach ($secaptos as $secapto) {
        // Encuentra todas las unidades que pertenecen a la seccion 
        $uns= Un::where('seccione_id', $secapto->seccione_id)
                ->where('activa', 1)->get();
        //dd($uns->toArray());

        // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento y de cuotas extraordinarioas
        foreach ($uns as $un) {
           // verifica si hay cuotas extraordinarias que aplicar a la seccion
          if ($secapto->f_iniciaextra) {
            $f_inicio = Carbon::parse($secapto->f_iniciaextra); // fecha en que inicia el cobro de la cuota extraordinaria
            $f_final = Carbon::parse($secapto->f_iniciaextra)->addMonths($secapto->extra_meses - 1); // fecha en que termina el cobro de la cuota extraordinaria
            //dd($f_inicio, $f_final, $fecha);

            if ($fecha->between($f_inicio, $f_final)) {
              // si la fecha del periodo en estudio esta entre la fecha de inicio y la fecha final,
              // quiere decir que hay que aplicar el cobro de la cuota extraordinaria a todas las unidades de la seccion en estudio
              $totalExtraordinaria = $totalExtraordinaria + floatval($secapto->extra);
            }
          }    
        } // end foreach $uns 
      } // end foreach secapto  

      // si encuentra total de cuotas extraordinarias registra en libros
      if ($totalExtraordinaria > 0) {
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo_id;
        $dato->fecha         = $fecha;
        $dato->detalle       = 'Cuota extraordinaria por cobrar';
        $dato->debito        = $totalExtraordinaria;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo_id;
        $dato->detalle      = '   Ingresos por cuota extraordinaria';
        $dato->credito      = $totalExtraordinaria;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo_id;
        $dato->detalle      = 'Para registrar resumen de ingresos por cuotas extraordinaria de '.$periodo.'- OC dia '.$dia;
        $dato->save(); 
      
        // Registra facturacion mensual de la unidad en cuenta 'Cuota de mantenimiento extraordinaria por cobrar'
        Sity::registraEnCuentas(
                $periodo_id, // periodo                      
                'mas',  // aumenta
                1,      // cuenta id
                16,     // '1110.00'
                $fecha,   // fecha
                'Resumen de Cuota de mantenimiento extraordinarias por cobrar '.$periodo.'- OC dia '.$dia, // detalle
                $totalExtraordinaria // monto
              );

        // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento extraordinarias' 4120.00
        Sity::registraEnCuentas(
                $periodo_id, // periodo
                'mas',    // aumenta
                4,        // cuenta id
                17,       // '4120.00'
                $fecha,   // fecha
                'Resumen de Ingreso por cuota de mantenimiento extraordinarias '.$periodo.'- OC dia '.$dia, // detalle
                $totalExtraordinaria // monto
              );
      } // end if 2
    } // end if 1
  } // end of function


} //fin de Class Npdo