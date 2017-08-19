<?php namespace App\Http\Controllers\Contabilidad;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\library\Sity;
use App\library\Npdo;
use App\library\Fact;
use App\library\Hojat;
use App\library\Ppago;

use Input, Session, Carbon\Carbon;
use Validator, DB, Cache, Date;

use App\Ctmayore;
use App\Ctmayorehi;
use App\Catalogo;
use App\Pcontable;
use App\Ht;
use App\Un;
use App\Secapto;
use App\Concilia;
use App\Cajachica;
use App\Bloque;
use App\Ctdasm;
use App\Ctdasmhi;
use App\Detallepagofactura;

class HojadetrabajosController extends Controller {
    
  public function __construct()
  {
      $this->middleware('hasAccess');    
  }
  
  /***********************************************************************************
  * Despliega el estado de resultado proyectado
  ************************************************************************************/ 
  public function estadoderesultado($pcontable_id) {
      
    // encuentra todas las cuentas de Ingresos de un determinado periodo contable
    $ingresos= Hojat::getDataParaEstadoResultado($pcontable_id, 4);
    //dd($ingresos);

    // encuentra todas las cuentas de Gastos de un determinado periodo contable
    $gastos= Hojat::getDataParaEstadoResultado($pcontable_id, 6);
    //dd($gastos);
            
    //calcula el total de la columna debito y el de la columna credito
    $totalIngresos = 0;
    $totalGastos = 0;
    
    // calcula en total de ingresos recibidos         
    foreach($ingresos as $ingreso) {
      $totalIngresos = $totalIngresos + $ingreso['saldo_credito'];
    }        
    
    foreach($gastos as $gasto) {
      // totales balance ajustado        
      $totalGastos = $totalGastos + $gasto['saldo_debito'];
    }
    //dd($totalIngresos, $totalGastos);
    
    // calcula la utilidad neta
    $utilidad= $totalIngresos - $totalGastos;

    return \View::make('contabilidad.estadoderesultado.estadoderesultado')
                ->with('periodo', Pcontable::find($pcontable_id)->periodo)
                ->with('ingresos', $ingresos)
                ->with('gastos', $gastos)
                ->with('totalIngresos', $totalIngresos)
                ->with('totalGastos', $totalGastos)
                ->with('utilidad', $utilidad);
  }

  /***********************************************************************************
  * Despliega el estado de resultado final
  ************************************************************************************/ 
  public function er($pcontable_id) {
  
    // encuentra todas las cuentas de Ingresos de un determinado periodo contable
    $ingresos= Hojat::getDataParaEstadoResultadoHis($pcontable_id, 4);
    //dd($ingresos);

    // encuentra todas las cuentas de Gastos de un determinado periodo contable
    $gastos= Hojat::getDataParaEstadoResultadoHis($pcontable_id, 6);
    //dd($gastos);
            
    //calcula el total de la columna debito y el de la columna credito
    $totalIngresos = 0;
    $totalGastos = 0;
    
    // calcula en total de ingresos recibidos         
    foreach($ingresos as $ingreso) {
      $totalIngresos = $totalIngresos + $ingreso['saldo_credito'];
    }        
    
    foreach($gastos as $gasto) {
      // totales balance ajustado        
      $totalGastos = $totalGastos + $gasto['saldo_debito'];
    }
    //dd($totalIngresos, $totalGastos);
    
    // calcula la utilidad neta
    $utilidad= $totalIngresos - $totalGastos;

    return \View::make('contabilidad.estadoderesultado.er')
                ->with('periodo', Pcontable::find($pcontable_id)->periodo)
                ->with('ingresos', $ingresos)
                ->with('gastos', $gastos)
                ->with('totalIngresos', $totalIngresos)
                ->with('totalGastos', $totalGastos)
                ->with('utilidad', $utilidad);
  }


  /***********************************************************************************
  * Despliega el estado de resultado final
  ************************************************************************************/ 
  public function facturasporpagar($pcontable_id) {
    
/*    $data = Org::has('facturas')->with(array(
        'facturas' => function($query) { $query->where('pagada', '=', 0); },
        'facturas.detallepagofacturas' => function($query) { $query->where('pagada', '=', 0); }
    ))->get();*/
    
    $datos = Detallepagofactura::where('pagada', '=', 0)->get();
    
    // calcula el total por pagar
    $totalPorPagar = $datos->sum('monto');
    //dd($totalPorPagar);
    
    $i = 0;
    foreach ($datos as $dato) {
      // agrega los datos a la collection
      $datos[$i]["afavorde"] = $dato->factura->afavorde;
      $datos[$i]["factura_no"] = $dato->factura->doc_no;
      $datos[$i]["f_pago"] = Date::parse($dato->fecha)->toFormattedDateString();
      $i++;
    }

    //dd($datos->toArray());

    $data = $datos->toJson();
    
    // Remove first and last char from string
    $data = substr($data, 1, -1);
    
    $data = str_replace(array('[',']'), '',$data);
    //dd($data); 
    
    return \View::make('contabilidad.detallepagofacturas.facturasporpagar')
            ->with('data', $data)
            ->with('totalPorPagar', $totalPorPagar);
  }  


  /***********************************************************************************
  * Despliega el balance general final
  ************************************************************************************/ 
  public function bg($pcontable_id) {
        
    //---------------------------------
    // SECCION BALANCE GENERAL
    //---------------------------------
    $activoCorrientes= Hojat::getDataParaBalanceGeneralHis($pcontable_id, 1, 1);
    //dd($activoCorrientes);        
    
    $activoNoCorrientes= Hojat::getDataParaBalanceGeneralHis($pcontable_id, 1, 0);
    //dd($activoNoCorrientes);        
    
    $pasivoCorrientes= Hojat::getDataParaBalanceGeneralHis($pcontable_id, 2, 1);
    //dd($pasivoCorrientes);        
    
    $pasivoNoCorrientes= Hojat::getDataParaBalanceGeneralHis($pcontable_id, 2, 0);
    //dd($pasivoNoCorrientes);        

    $patrimonios= Hojat::getDataParaBalanceGeneralHis($pcontable_id, 3, Null);
    //dd($patrimonios); 
 
    //calcula el total de cada uno de los tipos de cuentas
    $total_activoCorrientes= 0;
    $total_activoNoCorrientes= 0;
    $total_pasivoCorrientes= 0;
    $total_pasivoNoCorrientes= 0;
    $total_patrimonios= 0;

    foreach($activoCorrientes as $activoCorriente) {
      $total_activoCorrientes = $total_activoCorrientes + ($activoCorriente['saldo_debito'] - $activoCorriente['saldo_credito']);
    }

    foreach($activoNoCorrientes as $activoNoCorriente) {
      $total_activoNoCorrientes = $total_activoNoCorrientes + ($activoNoCorriente['saldo_debito'] - $activoNoCorriente['saldo_credito']);
    }

    foreach($pasivoCorrientes as $pasivoCorriente) {
       $total_pasivoCorrientes = $total_pasivoCorrientes + ($pasivoCorriente['saldo_credito'] - $pasivoCorriente['saldo_debito']);
    }

    foreach($pasivoNoCorrientes as $pasivoNoCorriente) {
      $total_pasivoNoCorrientes = $total_pasivoNoCorrientes + ($pasivoNoCorriente['saldo_credito'] - $pasivoNoCorriente['saldo_debito']);
    }

    foreach($patrimonios as $patrimonio) {
      $total_patrimonios = $total_patrimonios + ($patrimonio['saldo_credito'] - $patrimonio['saldo_debito']);
    }
    // dd($total_activoCorrientes, $total_activoNoCorrientes, $total_pasivoCorrientes, $total_pasivoNoCorrientes, $total_patrimonios);        

    $totalActivos = $total_activoCorrientes + $total_activoNoCorrientes;
    $totalPasivos = $total_pasivoCorrientes + $total_pasivoNoCorrientes;
    $totalPasivoPatrimonio = $totalPasivos + $total_patrimonios;       

    // calcula la Utilidad retenida del periodo
    //$utilidad= Sity::getUtilidadRetenida($pcontable_id); 
    
    return \View::make('contabilidad.balancegeneral.balancegeneral')
                ->with('activoCorrientes', $activoCorrientes)
                ->with('activoNoCorrientes', $activoNoCorrientes)
                ->with('pasivoCorrientes', $pasivoCorrientes)
                ->with('pasivoNoCorrientes', $pasivoNoCorrientes)
                ->with('patrimonios', $patrimonios)
                
                ->with('total_activoCorrientes', $total_activoCorrientes)
                ->with('total_activoNoCorrientes', $total_activoNoCorrientes)
                ->with('total_pasivoCorrientes', $total_pasivoCorrientes)
                ->with('total_pasivoNoCorrientes', $total_pasivoNoCorrientes)
                ->with('total_patrimonio', $total_patrimonios)                    
                
                ->with('totalActivos', $totalActivos)
                ->with('totalPasivos', $totalPasivos)
                ->with('totalPasivoPatrimonio', $totalPasivoPatrimonio)
                
                //->with('utilidad', $utilidad)
                ->with('periodo', Pcontable::find($pcontable_id)->periodo);
  }

  /***********************************************************************************
  * Despliega el balance general final
  ************************************************************************************/ 
  public function bg_m2_proyectado($pcontable_id) {
    //dd($pcontable_id);      
   
    //------------------------------------------------------
    //  Calcula Activos liquidos banco, caja generla y caja chica
    //------------------------------------------------------
    
    // encuentra el saldo actual del banco
    $saldoBanco = Sity::getSaldoCuenta(8, $pcontable_id); 
    //dd($saldoBanco);
    
    // encuentra el saldo actual de la caja general
    $cgeneralSaldo = Sity::getSaldoCuenta(32, $pcontable_id); 
    //dd($cgeneralSaldo);      
    
    // encuentra el saldo actual de la caja chica
    $cchicaSaldo = Sity::getSaldoCuenta(30, $pcontable_id); 
    //dd($cchicaSaldo);

    // calcula el total de activos liquido
    $totalLiquido = $saldoBanco + $cgeneralSaldo + $cchicaSaldo;

    //------------------------------------------------------
    //  Calcula Cuentas por cobrar
    //------------------------------------------------------    
    // encuentra todos los bloques
    $bloques = Bloque::all();
    //dd($bloques->toArray());

    $i=0;      
    $totalCuotasPorCobrar = 0;
    
    foreach ($bloques as $bloque) {
      // encuentra el total de cuota regular por cobrar de un bloque en especial
      $cuotaRegPorCobrar = Ctdasm::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('pagada', 0)->sum('importe');    
      //dd( $cuotaRegPorCobrar);

      // encuentra el total de cuota extraordinaria por cobrar de un bloque en especial
      $cuotaExtraPorCobrar = Ctdasm::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('extra_pagada', 0)->where('extra_siono', 1)->sum('extra');    

      // encuentra el total recargos por cobrar de un bloque en especial
      $recargoPorCobrar = Ctdasm::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('recargo_pagado', 0)->where('recargo_siono', 1)->sum('recargo'); 
    
       // Agrega la cantidad en almacen al array principal para enviar toda la información consolidada en un solo array
      $bloques[$i]["cuotaRegPorCobrar"] = $cuotaRegPorCobrar;
      $bloques[$i]["cuotaExtraPorCobrar"] = $cuotaExtraPorCobrar;
      $bloques[$i]["recargoPorCobrar"] = $recargoPorCobrar;
      $i++;
      
      // calcula el total de cuentas por cobrar
      $totalCuotasPorCobrar = $totalCuotasPorCobrar + ($cuotaRegPorCobrar + $cuotaExtraPorCobrar + $recargoPorCobrar);
    }

    //------------------------------------------------------
    //  Calcula otros Activos menos banco, caja general, caja chica, cuentas de mant regular, extraordinarias y recargos
    //------------------------------------------------------

    $cuentas = Catalogo::where('tipo', 1)
                      ->where('id', '!=', 8)  // banco
                      ->where('id', '!=', 30) // caja chica
                      ->where('id', '!=', 32) // caja general
                      ->where('id', '!=', 1)  // cuotas de mant regular por cobrar                        
                      ->where('id', '!=', 2)  // recargos en cuotas de mant regular por cobrar
                      ->where('id', '!=', 16) // cuotas de mant extraordinarias por cobrar
                      ->get();
    //dd($cuentas->toArray());

    $otrosActivos = array();
    $i = 0;
    
    foreach ($cuentas as $cuenta) {
      // encuentra el saldo actual de cada una de las cuentas
      $otrosActivos[$i]['nombre'] = $cuenta->nombre; 
      $otrosActivos[$i]['saldo'] = Sity::getSaldoCuenta($cuenta->id, $pcontable_id); 
      $i++;
    }
      
    // combierte el array a collection
    $otrosActivos = Collection::make($otrosActivos);
    
    // calcula el total de otros activos
    $totalOtrosActivos = $otrosActivos->sum('saldo');
    //dd($otrosActivos,  $totalOtrosActivos);


    //------------------------------------------------------
    //  Calcula pasivos
    //------------------------------------------------------
    $pasivos = Catalogo::where('tipo', 2)->get();
    //dd($pasivos->toArray());
    
    $i = 0;      
    $totalPasivos = 0;
    
    foreach ($pasivos as $pasivo) {

      // calcula el saldo actual de la cuenta
      $saldo = Sity::getSaldoCuenta($pasivo->id, $pcontable_id); 
    
      // Agrega saldo a la collection
      $pasivos[$i]['saldo'] = $saldo;  
      $i++;

      // calcula el total de pasivos
      $totalPasivos = $totalPasivos + $saldo;
    }
    //dd($pasivos->toArray());

    //------------------------------------------------------
    //  Calcula patrimonio
    //------------------------------------------------------
    $patrimonios = Catalogo::where('tipo', 3)->get();  // cuenta tipo patrimonio
    //dd($cta_pasivos);
    
    $i=0;      
    $totalPatrimonios = 0;
    foreach ($patrimonios as $patrimonio) {

      // calcula el saldo actual de la cuenta
      $saldo = Sity::getSaldoCuenta($patrimonio->id, $pcontable_id); 
    
       // Agrega la cantidad en almacen al array principal para enviar toda la información consolidada en un solo array
      $patrimonios[$i]['saldo'] = $saldo;  
      $i++;

      // calcula el total de cuentas por cobrar
      $totalPatrimonios = $totalPatrimonios + $saldo;
    }
    //dd($patrimonios->toArray());

    return \View::make('contabilidad.balancegeneral.bg_m2_proyectado')
              ->with('bloques', $bloques) 
              ->with('saldoBanco', $saldoBanco) 
              ->with('cgeneralSaldo', $cgeneralSaldo)                    
              ->with('cchicaSaldo', $cchicaSaldo)                     
              ->with('totalLiquido', $totalLiquido) 
              ->with('totalCuotasPorCobrar', $totalCuotasPorCobrar) 
              ->with('otrosActivos', $otrosActivos) 
              ->with('totalOtrosActivos', $totalOtrosActivos) 
              ->with('pasivos', $pasivos) 
              ->with('totalPasivos', $totalPasivos) 
              ->with('patrimonios', $patrimonios) 
              ->with('totalPatrimonios', $totalPatrimonios)
              ->with('periodo', Pcontable::find($pcontable_id));
  }


  /***********************************************************************************
  * Despliega el balance general final
  ************************************************************************************/ 
  public function bg_m2_final($pcontable_id) {
    //dd($pcontable_id);      
    
    //------------------------------------------------------
    //  Calcula Activos liquidos banco, caja generla y caja chica
    //------------------------------------------------------
    
    // encuentra el saldo actual del banco
    $saldoBanco = Sity::getSaldoCuentaHis(8, $pcontable_id); 
    //dd($saldoBanco);
    
    // encuentra el saldo actual de la caja general
    $cgeneralSaldo = Sity::getSaldoCuentaHis(32, $pcontable_id); 
    //dd($cgeneralSaldo);      
    
    // encuentra el saldo actual de la caja chica
    $cchicaSaldo = Sity::getSaldoCuentaHis(30, $pcontable_id); 
    //dd($cchicaSaldo);

    // calcula el total de activos liquido
    $totalLiquido = $saldoBanco + $cgeneralSaldo + $cchicaSaldo;

    //------------------------------------------------------
    //  Calcula Cuentas por cobrar
    //------------------------------------------------------    
    // encuentra todos los bloques
    $bloques = Bloque::all();
    //dd($bloques->toArray());

    $i=0;      
    $totalCuotasPorCobrar = 0;
    
    foreach ($bloques as $bloque) {
      // encuentra el total de cuota regular por cobrar de un bloque en especial
      $cuotaRegPorCobrar = Ctdasmhi::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('pagada', 0)->sum('importe');    
      //dd( $cuotaRegPorCobrar);

      // encuentra el total de cuota extraordinaria por cobrar de un bloque en especial
      $cuotaExtraPorCobrar = Ctdasmhi::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('extra_pagada', 0)->where('extra_siono', 1)->sum('extra');    

      // encuentra el total recargos por cobrar de un bloque en especial
      $recargoPorCobrar = Ctdasmhi::where('pcontable_id', '<=', $pcontable_id)->where('bloque_id', $bloque->id)->where('recargo_pagado', 0)->where('recargo_siono', 1)->sum('recargo'); 
    
       // Agrega la cantidad en almacen al array principal para enviar toda la información consolidada en un solo array
      $bloques[$i]["cuotaRegPorCobrar"] = $cuotaRegPorCobrar;
      $bloques[$i]["cuotaExtraPorCobrar"] = $cuotaExtraPorCobrar;
      $bloques[$i]["recargoPorCobrar"] = $recargoPorCobrar;
      $i++;
      
      // calcula el total de cuentas por cobrar
      $totalCuotasPorCobrar = $totalCuotasPorCobrar + ($cuotaRegPorCobrar + $cuotaExtraPorCobrar + $recargoPorCobrar);
    }

    //------------------------------------------------------
    //  Calcula otros Activos menos banco, caja general, caja chica, cuentas de mant regular, extraordinarias y recargos
    //------------------------------------------------------

    $cuentas = Catalogo::where('tipo', 1)
                      ->where('id', '!=', 8)  // banco
                      ->where('id', '!=', 30) // caja chica
                      ->where('id', '!=', 32) // caja general
                      ->where('id', '!=', 1)  // cuotas de mant regular por cobrar                        
                      ->where('id', '!=', 2)  // recargos en cuotas de mant regular por cobrar
                      ->where('id', '!=', 16) // cuotas de mant extraordinarias por cobrar
                      ->get();
    //dd($cuentas->toArray());

    $otrosActivos = array();
    $i = 0;
    
    foreach ($cuentas as $cuenta) {
      // encuentra el saldo actual de cada una de las cuentas
      $otrosActivos[$i]['nombre'] = $cuenta->nombre; 
      $otrosActivos[$i]['saldo'] = Sity::getSaldoCuentaHis($cuenta->id, $pcontable_id); 
      $i++;
    }
      
    // combierte el array a collection
    $otrosActivos = Collection::make($otrosActivos);
    
    // calcula el total de otros activos
    $totalOtrosActivos = $otrosActivos->sum('saldo');
    //dd($otrosActivos,  $totalOtrosActivos);


    //------------------------------------------------------
    //  Calcula pasivos
    //------------------------------------------------------
    $pasivos = Catalogo::where('tipo', 2)->get();
    //dd($pasivos->toArray());
    
    $i = 0;      
    $totalPasivos = 0;
    
    foreach ($pasivos as $pasivo) {

      // calcula el saldo actual de la cuenta
      $saldo = Sity::getSaldoCuentaHis($pasivo->id, $pcontable_id); 
    
      // Agrega saldo a la collection
      $pasivos[$i]['saldo'] = $saldo;  
      $i++;

      // calcula el total de pasivos
      $totalPasivos = $totalPasivos + $saldo;
    }
    //dd($pasivos->toArray());

    //------------------------------------------------------
    //  Calcula patrimonio
    //------------------------------------------------------
    $patrimonios = Catalogo::where('tipo', 3)->get();  // cuenta tipo patrimonio
    //dd($cta_pasivos);
    
    $i=0;      
    $totalPatrimonios = 0;
    foreach ($patrimonios as $patrimonio) {

      // calcula el saldo actual de la cuenta
      $saldo = Sity::getSaldoCuentaHis($patrimonio->id, $pcontable_id); 
    
       // Agrega la cantidad en almacen al array principal para enviar toda la información consolidada en un solo array
      $patrimonios[$i]['saldo'] = $saldo;  
      $i++;

      // calcula el total de cuentas por cobrar
      $totalPatrimonios = $totalPatrimonios + $saldo;
    }
    //dd($patrimonios->toArray());

    return \View::make('contabilidad.balancegeneral.bg_m2_final')
              ->with('bloques', $bloques) 
              ->with('saldoBanco', $saldoBanco) 
              ->with('cgeneralSaldo', $cgeneralSaldo)                    
              ->with('cchicaSaldo', $cchicaSaldo)                     
              ->with('totalLiquido', $totalLiquido) 
              ->with('totalCuotasPorCobrar', $totalCuotasPorCobrar) 
              ->with('otrosActivos', $otrosActivos) 
              ->with('totalOtrosActivos', $totalOtrosActivos) 
              ->with('pasivos', $pasivos) 
              ->with('totalPasivos', $totalPasivos) 
              ->with('patrimonios', $patrimonios) 
              ->with('totalPatrimonios', $totalPatrimonios)
              ->with('periodo', Pcontable::find($pcontable_id));
  }


  /***********************************************************************************
  * Despliega el balance general proyectado
  ************************************************************************************/ 
  public function balancegeneral($pcontable_id, $periodo) {
      
    //---------------------------------
    // SECCION BALANCE GENERAL
    //---------------------------------
    $activoCorrientes= Hojat::getDataParaBalanceGeneral($pcontable_id, 1, 1);
    //dd($activoCorrientes);        
    
    $activoNoCorrientes= Hojat::getDataParaBalanceGeneral($pcontable_id, 1, 0);
    //dd($activoNoCorrientes);        
    
    $pasivoCorrientes= Hojat::getDataParaBalanceGeneral($pcontable_id, 2, 1);
    //dd($pasivoCorrientes);        
    
    $pasivoNoCorrientes= Hojat::getDataParaBalanceGeneral($pcontable_id, 2, 0);
    //dd($pasivoNoCorrientes);        

    $patrimonios= Hojat::getDataParaBalanceGeneral($pcontable_id, 3, Null);
    //dd($patrimonios); 
 
    //calcula el total de cada uno de los tipos de cuentas
    $total_activoCorrientes= 0;
    $total_activoNoCorrientes= 0;
    $total_pasivoCorrientes= 0;
    $total_pasivoNoCorrientes= 0;
    $total_patrimonios= 0;

    foreach($activoCorrientes as $activoCorriente) {
      $total_activoCorrientes = $total_activoCorrientes + ($activoCorriente['saldo_debito'] - $activoCorriente['saldo_credito']);
    }
//dd($total_activoCorrientes);
    foreach($activoNoCorrientes as $activoNoCorriente) {
      $total_activoNoCorrientes = $total_activoNoCorrientes + ($activoNoCorriente['saldo_debito'] - $activoNoCorriente['saldo_credito']);
    }

    foreach($pasivoCorrientes as $pasivoCorriente) {
       $total_pasivoCorrientes = $total_pasivoCorrientes + ($pasivoCorriente['saldo_credito'] - $pasivoCorriente['saldo_debito']);
    }

    foreach($pasivoNoCorrientes as $pasivoNoCorriente) {
      $total_pasivoNoCorrientes = $total_pasivoNoCorrientes + ($pasivoNoCorriente['saldo_credito'] - $pasivoNoCorriente['saldo_debito']);
    }

    foreach($patrimonios as $patrimonio) {
      $total_patrimonios = $total_patrimonios + ($patrimonio['saldo_credito'] - $patrimonio['saldo_debito']);
    }
    // dd($total_activoCorrientes, $total_activoNoCorrientes, $total_pasivoCorrientes, $total_pasivoNoCorrientes, $total_patrimonios);        

    $totalActivos = $total_activoCorrientes + $total_activoNoCorrientes;
    $totalPasivos = $total_pasivoCorrientes + $total_pasivoNoCorrientes;
    $totalPasivoPatrimonio = $totalPasivos + $total_patrimonios;       

    // calcula la Utilidad retenida del periodo
    //$utilidad= Sity::getUtilidadRetenida($pcontable_id); 
    
    return \View::make('contabilidad.balancegeneral.balancegeneral')
                ->with('activoCorrientes', $activoCorrientes)
                ->with('activoNoCorrientes', $activoNoCorrientes)
                ->with('pasivoCorrientes', $pasivoCorrientes)
                ->with('pasivoNoCorrientes', $pasivoNoCorrientes)
                ->with('patrimonios', $patrimonios)
                
                ->with('total_activoCorrientes', $total_activoCorrientes)
                ->with('total_activoNoCorrientes', $total_activoNoCorrientes)
                ->with('total_pasivoCorrientes', $total_pasivoCorrientes)
                ->with('total_pasivoNoCorrientes', $total_pasivoNoCorrientes)
                ->with('total_patrimonio', $total_patrimonios)                    
                
                ->with('totalActivos', $totalActivos)
                ->with('totalPasivos', $totalPasivos)
                ->with('totalPasivoPatrimonio', $totalPasivoPatrimonio)
                
                //->with('utilidad', $utilidad)
                ->with('periodo', $periodo);
  }

  /***********************************************************************************
  * Despliega el mayor auxiliar de una determinada cuenta
  ************************************************************************************/   
  public function verMayorAux($periodo, $cuenta) {

    $datos = Ctmayore::where('pcontable_id', $periodo)
                     ->where('cuenta', $cuenta)
                     ->get();

    //dd($datos->toArray());      

    $data=array();    
    $i=1;
    
    foreach ($datos as $dato) {
      if ($dato->tipo == 1 || $dato->tipo == 6) {  
        if ($i==1) {
          $saldo = $dato->debito - $dato->credito;
          $datas[$i]['fecha']= $dato->fecha;
          $datas[$i]['codigo']= $dato->codigo;
          $datas[$i]['detalle']= $dato->detalle;
          $datas[$i]['ref']= "";   
          $datas[$i]['debito']= $dato->debito;
          $datas[$i]['credito']= $dato->credito;
          $datas[$i]['saldo']=  $saldo;
            
        } else {
          $saldo = ($dato->debito - $dato->credito) + $saldo;
          $datas[$i]['fecha']= $dato->fecha;
          $datas[$i]['codigo']= $dato->codigo;
          $datas[$i]['detalle']= $dato->detalle;
          $datas[$i]['ref']= ""; 
          $datas[$i]['debito']= $dato->debito;
          $datas[$i]['credito']= $dato->credito;
          $datas[$i]['saldo']=  $saldo;
        }       

      } elseif ($dato->tipo == 2 || $dato->tipo == 3 || $dato->tipo == 4) {  
          if ($i==1) {
              $saldo = $dato->credito - $dato->debito;
              $datas[$i]['fecha']= $dato->fecha;
              $datas[$i]['codigo']= $dato->codigo;
              $datas[$i]['detalle']= $dato->detalle;
              $datas[$i]['ref']= "";
              $datas[$i]['debito']= $dato->debito;
              $datas[$i]['credito']= $dato->credito;
              $datas[$i]['saldo']=  $saldo;
              
          } else {
              $saldo = ($dato->credito - $dato->debito) + $saldo;
              $datas[$i]['fecha']= $dato->fecha;
              $datas[$i]['codigo']= $dato->codigo;
              $datas[$i]['detalle']= $dato->detalle;
              $datas[$i]['ref']= "";
              $datas[$i]['debito']= $dato->debito;
              $datas[$i]['credito']= $dato->credito;
              $datas[$i]['saldo']=  $saldo;
          }       

      } else {
        return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
      }
      $i++;
    }
    //dd($datas);

    $cuenta= Catalogo::find($cuenta);
    return view('contabilidad.hojadetrabajos.verMayorAux')
            ->with('datas', $datas)
            ->with('cuenta', $cuenta);
  }


  /***********************************************************************************
  * Despliega el mayor auxiliar de una determinada cuenta
  ************************************************************************************/   
  public function verMayorAuxHis($periodo, $cuenta) {

    $datos = Ctmayorehi::where('pcontable_id', $periodo)
                   ->where('cuenta', $cuenta)
                   ->get();
    //dd($periodo, $cuenta, $datos->toArray());
    
    $data = array();    
    $i = 1;
    
    foreach ($datos as $dato) {
      if ($dato->tipo == 1 || $dato->tipo == 6) {  
        if ($i==1) {
          $saldo = $dato->debito - $dato->credito;
          $datas[$i]['fecha']= $dato->fecha;
          $datas[$i]['codigo']= $dato->codigo;
          $datas[$i]['detalle']= $dato->detalle;
          $datas[$i]['ref']= "";   
          $datas[$i]['debito']= $dato->debito;
          $datas[$i]['credito']= $dato->credito;
          $datas[$i]['saldo']=  $saldo;
            
        } else {
          $saldo = ($dato->debito - $dato->credito) + $saldo;
          $datas[$i]['fecha']= $dato->fecha;
          $datas[$i]['codigo']= $dato->codigo;
          $datas[$i]['detalle']= $dato->detalle;
          $datas[$i]['ref']= ""; 
          $datas[$i]['debito']= $dato->debito;
          $datas[$i]['credito']= $dato->credito;
          $datas[$i]['saldo']=  $saldo;
        }       

      } elseif ($dato->tipo == 2 || $dato->tipo == 3 || $dato->tipo == 4) {  
          if ($i==1) {
              $saldo = $dato->credito - $dato->debito;
              $datas[$i]['fecha']= $dato->fecha;
              $datas[$i]['codigo']= $dato->codigo;
              $datas[$i]['detalle']= $dato->detalle;
              $datas[$i]['ref']= "";
              $datas[$i]['debito']= $dato->debito;
              $datas[$i]['credito']= $dato->credito;
              $datas[$i]['saldo']=  $saldo;
              
          } else {
              $saldo = ($dato->credito - $dato->debito) + $saldo;
              $datas[$i]['fecha']= $dato->fecha;
              $datas[$i]['codigo']= $dato->codigo;
              $datas[$i]['detalle']= $dato->detalle;
              $datas[$i]['ref']= "";
              $datas[$i]['debito']= $dato->debito;
              $datas[$i]['credito']= $dato->credito;
              $datas[$i]['saldo']=  $saldo;
          }       

      } else {
        return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
      }
      $i++;
    }
    //dd($datas);

    $cuenta= Catalogo::find($cuenta);
    return view('contabilidad.hojadetrabajos.verMayorAux')
            ->with('datas', $datas)
            ->with('unCodigo', 0)
            ->with('cuenta', $cuenta);
  }


  /** 
  *==================================================================================================
  * Cierra definitivamente un determinado periodo contable
  * @param  string     $periodo_id    "3"
  * @param  string     $periodo       "Mar-2016"
  * @param  string     $fecha         "2016-03-01"
  * @return void
  **************************************************************************************************/
  public function cierraPeriodo($pcontable_id, $periodo, $fecha) {
  //dd($pcontable_id, $periodo, $fecha);

    DB::beginTransaction();
    try {
      
      // verifica que todas las unidades esten debidamente inicializadas
      $datos= Un::where('inicializada', 0)->first();
      if ($datos) {
        Session::flash('danger', 'Hay algunas unidades que no han sido inicializadas, antes de cerrar el periodo debera inicializar todas las unidades!');
        //return redirect()->route('pcontables.index');
      }

      // verifica si el nuevo periodo ya existe
      $newPeriodo= Pcontable::find($pcontable_id + 1);        
      //dd($newPeriodo);

      // Construye la fecha del periodo real
      $year= Carbon::today()->year;
      $month= Carbon::today()->month;
      $periodoReal= Carbon::createFromDate($year, $month, 1);

      // calcula cual seria la fecha del nuevo periodo si se llegara a crear
      $fecha= Carbon::parse($fecha);
      $fechaNuevoPeriodo = Carbon::parse($fecha)->addMonth();

      //dd($periodoReal, $fecha, $newPeriodo);

      // si la fecha del nuevo periodo no es igual a la fecha del periodo real,
      // entonces se procede a cerrar el periodo actual y crear el nuevo periodo
      if (($periodoReal->ne($fecha)) && !$newPeriodo) {
        
        // penaliza antes de cerrar el periodo
        Hojat::penalizarTipo1($fecha, $pcontable_id); 
        
        // procede a cerrar el periodo actual              
        $fnext= clone $fecha;
        $fnext= $fnext->addMonth();
       
        // almacena datos del periodo antes de cerrarlo y las almacena en la tabla Hts (hoja de trabajo historica)
        //Hojat::migraDatosHts($pcontable_id);

        // cierra todas la cuentas nominales o temporales por finalizacion de periodo contable
        Hojat::cierraCuentasTemp($pcontable_id, $fecha);

        // inicializa las cuentas permanentes en periodo posterior
        Hojat::inicializaCuentasPerm($pcontable_id, $fnext);

        // cierra el periodo contable
        $pc= Pcontable::find($pcontable_id);
        $pc->cerrado = 1;
        $pc->f_cierre = $fecha->endOfMonth();
        $pc->save();
        
        // Registra en bitacoras
        $detalle = 'Cierra periodo contable de '.$periodo;
        $tabla = 'pcontables';
        $registro = $pcontable_id;
        $accion = 'Cierra periodo contable';
        
        Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);
        
        // migra los datos de ctmayores a la tabla de datos historicos ctmayorehi y 
        // posteriormente los borra de la tabla ctmayores
        Hojat::migraDatosCtmayorehis($pcontable_id);

        // migra los datos de ctdiarios a la tabla de datos historicos ctdiariohis y 
        // posteriormente los borra de la tabla ctdiarios
        Hojat::migraDatosCtdiariohis($pcontable_id);
        
        // migra los datos de ctdasms a la tabla de datos historicos ctdasmhis
        Hojat::migraDatosCtdasmhis($pcontable_id);        

        // crea el nuevo periodo contable
        $newPeriodo = Npdo::periodo($fechaNuevoPeriodo);

        // crea conciliacion bancaria para el periodo
        $conicilia = new Concilia;
        $conicilia->pcontable_id = $newPeriodo;
        $conicilia->f_incioperiodo = $fechaNuevoPeriodo;
        $conicilia->slib_endlastpdo = Sity::getSaldoCuentaLastPcontable(8, 1); // encuentra el saldo de la cuenta banco del periodo anterior
        $conicilia->sban_endpresentpdo = 0;
        $conicilia->save();

        DB::commit();    
        
        // verifica si se creo con exito el nuevo periodo
        $newPeriodo= Pcontable::find($pcontable_id + 1);        
        //dd($newPeriodo);

        if ($newPeriodo) {

          $year= $fechaNuevoPeriodo->year;
          $month= $fechaNuevoPeriodo->month;

          // crea facturacion para el nuevo periodo contable
          // facturacion para las secciones que generan las ordenes de cobro los dias 1
          Fact::facturar(Carbon::createFromDate($year, $month, 1));

          // facturacion para las secciones que generan las ordenes de cobro los dias 16
          Fact::facturar(Carbon::createFromDate($year, $month, 16));

          // Encuentra todas las unidades que pertenecen a la seccion 
          $uns= Un::where('activa', 1)->get();
          // dd($uns->toArray());

          // Por cada apartamento que exista, verifica si se puede realizar pagos de cuotas o recargos utilizando solamente
          // el contenido de la cuenta de pagos anticipados de la unidad.        
          foreach ($uns as $un) {
            Ppago::iniciaPago($un->id, $fechaNuevoPeriodo, $newPeriodo->id, $newPeriodo->periodo);
          }  

          DB::commit();         

          Session::flash('success', 'Periodo '.$periodo.' ha sido cerrado permanentemente!');
          return redirect()->route('pcontables.index');        
        }

      } else {
        Session::flash('warning', 'No se puede cerrar el presente periodo ya que existe un periodo posterios!');
        return redirect()->route('pcontables.index'); 
      }

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo HojadetrabajosController.cierraPeriodo, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }

  /***********************************************************************************
  * Despliega la hoja de trabajo proyectada de un determinado periodo
  ************************************************************************************/ 
  public function htProyectada($pcontable_id) {
    //dd($pcontable_id);
    
    // encuentra los datos de periodo
    $periodo= Pcontable::find($pcontable_id);
    
    // encuentra los datos de la hoja de trabajo para un periodo determinado
    //$data = Hojat::getDataParaHojaDeTrabajo($pcontable_id);
    //$data = Hojat::getDataParaMigrarHtHistoricos($pcontable_id);    
    $data = Hojat::getDataParaHtProyectada($pcontable_id);  // ok   
    //dd($data);
    
    $datos = Collection::make($data);
    //dd($datos);
    
    $utilidad = $datos->sum('er_credito') - $datos->sum('er_debito'); 
    //dd($datos->sum('er_credito'), $datos->sum('er_debito'), $utilidad);

    $total_ba_debito = round((float)$datos->sum('saldoAjustado_debito'), 2);
    $total_ba_credito = round((float)$datos->sum('saldoAjustado_credito'), 2);
    //dd($total_ba_debito, $total_ba_credito);
    
    // verifica si el presente periodo admite ajustes, solo se permiten
    // hacer ajustes si se cumplen las siguientes condiciones:
    // 1. Si el periodo esta abierto
    // 2. Si el periodo previo esta cerrado
    // 3. Debe haber balance entre $total_ba_debito y $total_ba_credito
    
    // verifica si exite balance entre el $total_aj_debito y $total_aj_credito
    if ($total_ba_debito == $total_ba_credito) {
      $p3 = true;     
    } else {
      $p3 = false;  
    }
    
    // verifica si se trata del primer periodo en la base de datos y no esta cerrado
    if ($pcontable_id == 1 && $p3 == true) {
      $permitirAjustes= 'Si';
      $permitirCerrar= 'Si';
    
    } elseif ($pcontable_id == 1 && $p3 == false) {
      $permitirAjustes= 'Si';
      $permitirCerrar= 'No';
    } else {

      // verifica si el periodo esta abierto
      $p1 = Pcontable::where('id', $pcontable_id)->first()->cerrado;
      
      // verifica si el periodo previo esta cerrado
      $p2 = Pcontable::where('id', ($pcontable_id - 1))->first()->cerrado;
      //dd($p1, $p2);
      
      // permitir ajustes 
      if ($p1 == 0 && $p2 == 1) {
        $permitirAjustes = 'Si';
      
      } else {
        $permitirAjustes = 'No';
      }
      //dd($permitirAjustes);

      // verifica si el presente periodo admite ser cerrado, solo se permiten
      // cerrar un periodo si se cumplen las siguientes condiciones:
      // 1. Si se trata de un periodo no esta cerrado
      // 2. El periodo anterior debe estar cerrado       
      // 3. Debe haber balance entre $total_ba_debito y $total_ba_credito
      // 4. Si se trata del periodo previo al periodo real pero el periodo real aun no existe

      // Construye la fecha del periodo real
      $yearReal = Carbon::today()->year;
      $monthReal = Carbon::today()->month;
      $pdoReal= Sity::getMonthName($monthReal).'-'.$yearReal; 
      
      // Construye la fecha del periodo actual mas un mes
      $year = Carbon::parse($periodo->fecha)->year;
      $month = Carbon::parse($periodo->fecha)->addMonth()->month;
      $pdo = Sity::getMonthName($month).'-'.$year; 

      // verifica si el periodo real ya existe
      $periodoRealExiste = Pcontable::where('periodo', $pdoReal)->first();
      //dd($periodoRealExiste, $pdoReal, $pdo);
      
      // Si se trata de periodo previo al periodo real y el periodo real no existe no debe permitir cerrar
      $p4 = 'Si';
      if ($pdoReal == $pdo) {
        if (!$periodoRealExiste) {
            $p4 = 'No';
        }
      } elseif ($periodoRealExiste) {
        if (!Pcontable::where('periodo', $pdo)->first()) {
            $p4 = 'No';
        }
      }
      //dd($p1, $p2, $p3, $p4);
      
      // permite cerrar 
      if ($p1 == 0 && $p2 == 1 && $p3 && $p4 == 'Si') {
        $permitirCerrar = 'Si';
      } else {
        $permitirCerrar = 'No';
      }
    }
    //dd($permitirCerrar);

    return \View::make('contabilidad.hojadetrabajos.htProyectada')
                ->with('datos', $datos)
                ->with('total_bp_debito', $datos->sum('saldo_debito'))                  
                ->with('total_bp_credito', $datos->sum('saldo_credito')) 
                ->with('total_aj_debito', $datos->sum('saldoAjustado_debito'))                  
                ->with('total_aj_credito', $datos->sum('saldoAjustado_credito')) 
                ->with('total_ba_debito', $total_ba_debito)                  
                ->with('total_ba_credito', $total_ba_credito) 
                ->with('total_er_debito', $datos->sum('er_debito'))                  
                ->with('total_er_credito', $datos->sum('er_credito')) 
                ->with('total_bg_debito', $datos->sum('bg_debito'))                  
                ->with('total_bg_credito', $datos->sum('bg_credito')) 
                ->with('utilidad', $utilidad) 
                ->with('permitirAjustes', $permitirAjustes) 
                ->with('permitirCerrar', $permitirCerrar) 
                ->with('periodo', $periodo);
  } 

  /***********************************************************************************
  * Despliega la hoja de trabajo proyectada de un determinado periodo
  ************************************************************************************/ 
  public function htFinal($pcontable_id) {
    //dd($pcontable_id);
    
    // encuentra los datos de periodo
    $periodo= Pcontable::find($pcontable_id);
    
    // encuentra los datos de la hoja de trabajo para un periodo determinado  
    $data = Hojat::getDataParaHtFinal($pcontable_id);  // ok   
    //dd($data);
    
    // calcula la utilidad del periodo
    $utilidad = Collection::make($data);
    $utilidad =  $utilidad->sum('er_credito') - $utilidad->sum('er_debito'); 
    //dd($datos->sum('er_credito'), $datos->sum('er_debito'), $utilidad)    

    // ajusta la utilidad acumulada al valor real.
    // Cuando se cierra un periodo la cuenta de Utilidad acumulada incluye la 
    // utilidad del periodo, por esa razon hay que restarle la utilidad del periodo actual 
    // para que refleje en la hoja de trabajo su valor real.
    
    $i = 0; 
    foreach ($data as $dto) {
      if ($dto['cuenta'] == 7) {
        $data[$i]['saldo_credito'] = $dto['saldo_credito'] - $utilidad;
        $data[$i]['saldoAjustado_credito'] = $dto['saldoAjustado_credito'] - $utilidad;
        $data[$i]['bg_credito'] = $dto['bg_credito'] - $utilidad;        
      }
      $i++;
    }    

    // convierte el array en colleccion para facilitar el calculo de totales
    $datos = Collection::make($data);
    //dd($datos);    
    
    $total_ba_debito = round((float)$datos->sum('saldoAjustado_debito'), 2);
    $total_ba_credito = round((float)$datos->sum('saldoAjustado_credito'), 2);
    //dd($total_ba_debito, $total_ba_credito);
        
    return \View::make('contabilidad.hojadetrabajos.htFinal')
                ->with('datos', $datos)
                ->with('total_bp_debito', $datos->sum('saldo_debito'))                  
                ->with('total_bp_credito', $datos->sum('saldo_credito')) 
                ->with('total_aj_debito', $datos->sum('saldoAjustado_debito'))                  
                ->with('total_aj_credito', $datos->sum('saldoAjustado_credito')) 
                ->with('total_ba_debito', $total_ba_debito)                  
                ->with('total_ba_credito', $total_ba_credito) 
                ->with('total_er_debito', $datos->sum('er_debito'))                  
                ->with('total_er_credito', $datos->sum('er_credito')) 
                ->with('total_bg_debito', $datos->sum('bg_debito'))                  
                ->with('total_bg_credito', $datos->sum('bg_credito')) 
                ->with('utilidad', $utilidad) 
                ->with('periodo', $periodo);
  }  

} // fin de controller