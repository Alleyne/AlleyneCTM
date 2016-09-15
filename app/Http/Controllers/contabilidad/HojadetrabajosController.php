<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\library\Sity;
use Input, Session, Redirect, Str, Carbon\Carbon, URL;
use Validator, View;
use Debugbar;

use App\Ctmayore;
use App\Catalogo;
use App\Pcontable;
use App\Ht;

class HojadetrabajosController extends Controller {
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }
    
    /***********************************************************************************
    * Despliega hoja de trabajo proyectada
    ************************************************************************************/	
	  public function show($pcontable_id) {
        $periodo= Pcontable::find($pcontable_id);
        //dd($periodo->id);
        
        // encuentra la data necesaria para confeccionar la hoja de trabajo
        $datos= Sity::getDataParaHojaDeTrabajo($periodo->id);
        //dd($datos);
        
        // calcula el total de la columna debito y el de la columna credito en el balance de pruebas
        $totalDebito = 0;
        $totalCredito = 0;    
        
        //calcula el total de la columna debito y el de la columna credito de ajustes
        $totalAjusteDebito = 0;
        $totalAjusteCredito = 0;
        
        //calcula el total de la columna debito y el de la columna credito en el balance ajustado
        $totalAjustadoDebito = 0;
        $totalAjustadoCredito = 0;
        
        foreach($datos as $dato) {
          // totales de balance de prueba
          $totalDebito += $dato['saldo_debito'];
          $totalCredito += $dato['saldo_credito'];    
          // totales de ajustes
          $totalAjusteDebito += $dato['saldoAjuste_debito'];    
          $totalAjusteCredito += $dato['saldoAjuste_credito'];
        
          $totalAjustadoDebito += $dato['saldoAjustado_debito'];
          $totalAjustadoCredito += $dato['saldoAjustado_credito'];
        }

        //dd($totalAjustadoDebito, $totalAjustadoCredito);
        $totalAjustadoDebito= round($totalAjustadoDebito,2);
        $totalAjustadoCredito= round($totalAjustadoCredito,2);
        
        // verifica si el presente periodo admite ajustes, solo se permiten
        // hacer ajustes si se cumplen las siguientes condiciones:
        // 1. Si se trata del primer periodo y el mismo no esta cerrado
        // 2. Solo se permitira hacer ajustes a un periodo si el previo esta cerrado

        // verifica si se trata del primer periodo en la base de datos y no esta cerrado
        if ($pcontable_id==1) {
            $p1= Pcontable::where('id', 1)
                          ->where('cerrado', 0)->first();
            if ($p1) {
                $p1='Si';
                $p2='Si';
            }
        } else {
            // verifica si el periodo anterior esta cerrado
            $p2= Pcontable::where('id', ($pcontable_id-1))
                          ->where('cerrado', 1)->first();
            if ($p2) {
                $p1='Si';
                $p2='Si';
 
             } else {
                $p1='No';
                $p2='No';
            }
        
        }
        //dd($p1, $p2);
        
        // permitir ajustes 
        if ($p1=='Si' && $p2=='Si') {
            $permitirAjustes= 'Si';
        } elseif ($pcontable_id>1 && $p2=='Si') {
            $permitirAjustes= 'Si';
        } else {
            $permitirAjustes= 'No';
        }
        //dd($permitirAjustes);
        
        // verifica si el presente periodo admite ser cerrado, solo se permiten
        // cerrar un periodo si se cumplen las siguientes condiciones:
        // 1. Si se trata del primer periodo y el mismo no esta cerrado
        // 2. El periodo anterior debe estar cerrado       
        // 3. Exista un periodo posterior abierto
        // 4. Debe haber balance entre $totalAjustadoDebito y $totalAjustadoCredito

        // verifica si exite un o mas periodo posteriores abiertos
        $p3= Pcontable::where('id', ($pcontable_id+1))
                      ->where('cerrado', 0)->first();
        if ($p3) {
            $p3='Si';
        } else {
            $p3='No';
        }
        //dd($p3);        
        
        // verifica si exite balance entre el $totalAjustadoDebito y $totalAjustadoCredito
        if ($totalAjustadoDebito == $totalAjustadoCredito) {
            $p4='Si';
        } else {
            $p4='No';
        }
        //dd($p4);        
     

        // permite cerrar 
        if (($p1=='Si' && $p3=='Si' && $p4=='Si') || ($p2=='Si' && $p3=='Si' && $p4=='Si')) {
            $permitirCerrar= 'Si';
         } else {
            $permitirCerrar= 'No';
        }

        //dd($datos);
        
        return \View::make('contabilidad.hojadetrabajos.show')
                    ->with('periodo', $periodo)
                    ->with('permitirAjustes', $permitirAjustes)
                    ->with('permitirCerrar', $permitirCerrar)
                    ->with('datos', $datos)
                    ->with('totalDebito', number_format($totalDebito,2))
                    ->with('totalCredito', number_format($totalCredito,2))
                    ->with('totalAjusteDebito', number_format($totalAjusteDebito,2))
                    ->with('totalAjusteCredito', number_format($totalAjusteCredito,2))
                    ->with('totalAjustadoDebito', number_format($totalAjustadoDebito,2))
                    ->with('totalAjustadoCredito', number_format($totalAjustadoCredito,2));
    }	

    /***********************************************************************************
    * Despliega el estado de resultado proyectado
    ************************************************************************************/ 
    public function estadoderesultado($pcontable_id) {
        
        // encuentra todas las cuentas de Ingresos de un determinado periodo contable
        $ingresos= Sity::getDataParaEstadoResultado($pcontable_id, 4);
        //dd($ingresos);

        // encuentra todas las cuentas de Gastos de un determinado periodo contable
        $gastos= Sity::getDataParaEstadoResultado($pcontable_id, 6);
        //dd($gastos);
                
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
        
        $ingresos = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 4)
                    ->get();
        //dd($ingresos->toArray());
        
        $gastos = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 6)
                    ->get();
        //dd($ingresos->toArray());      

        $utilidad= $ingresos->sum('er_credito')-$gastos->sum('er_debito'); 
        //dd($utilidad);

        return \View::make('contabilidad.estadoderesultado.er')
                    ->with('ingresos', $ingresos)
                    ->with('gastos', $gastos)
                    ->with('total_ingresos', $ingresos->sum('er_credito'))                  
                    ->with('total_gastos', $gastos->sum('er_debito')) 
                    ->with('utilidad', $utilidad) 
                    ->with('periodo', Pcontable::find($pcontable_id)->periodo);
    }

    /***********************************************************************************
    * Despliega el balance general final
    ************************************************************************************/ 
    public function bg($pcontable_id) {
        
        $activoCorrientes = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 1)
                    ->where('clase', 1)
                    ->get();
        //dd($activoCorrientes->toArray());
        
        $activoNoCorrientes = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 1)
                    ->where('clase', Null)
                    ->get();
        //dd($activoNoCorrientes->toArray());

        $pasivoCorrientes = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 2)
                    ->where('clase', 1)
                    ->get();
        //dd($pasivoCorrientes->toArray());      

        $pasivoNoCorrientes = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 2)
                    ->where('clase', Null)
                    ->get();
        //dd($pasivoCorrientes->toArray()); 

        $patrimonios = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 3)
                    ->get();
        //dd($patrimonios->toArray()); 
     
        // calcula Utilidad del periodo en estudio
        $total_ingresos = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 4)
                    ->sum('ba_credito');
        //dd($total_ingresos);

        // calcula Utilidad del periodo en estudio
        $total_gastos = Ht::where('pcontable_id', $pcontable_id)
                    ->where('tipo', 6)
                    ->sum('ba_debito');
        //dd($total_gastos);
        
        $utilidad= $total_ingresos- $total_gastos;          
       
        return \View::make('contabilidad.balancegeneral.bg')
                    ->with('activoCorrientes', $activoCorrientes)
                    ->with('activoNoCorrientes', $activoNoCorrientes)
                    
                    ->with('pasivoCorrientes', $pasivoCorrientes)
                    ->with('pasivoNoCorrientes', $pasivoNoCorrientes)
                    
                    ->with('patrimonios', $patrimonios)

                    ->with('total_activoCorrientes', $activoCorrientes->sum('bg_debito'))                  
                    ->with('total_activoNoCorrientes', $activoNoCorrientes->sum('bg_debito')) 
                    
                    ->with('total_pasivoCorrientes', $pasivoCorrientes->sum('bg_credito'))                  
                    ->with('total_pasivoNoCorrientes', $pasivoNoCorrientes->sum('bg_credito'))                     
                    
                    ->with('total_patrimonios', $patrimonios->sum('bg_credito'))                     

                    ->with('utilidad', $utilidad) 
                    ->with('periodo', Pcontable::find($pcontable_id)->periodo);
    }

    /***********************************************************************************
    * Despliega el balance general proyectado
    ************************************************************************************/ 
    public function balancegeneral($pcontable_id, $periodo) {
        
        //---------------------------------
        // SECCION BALANCE GENERAL
        //---------------------------------
        $activoCorrientes= Sity::getDataParaBalanceGeneral($pcontable_id, 1, 1);
        //dd($activoCorrientes);        
        
        $activoNoCorrientes= Sity::getDataParaBalanceGeneral($pcontable_id, 1, 0);
        //dd($activoNoCorrientes);        
        
        $pasivoCorrientes= Sity::getDataParaBalanceGeneral($pcontable_id, 2, 1);
        //dd($pasivoCorrientes);        
        
        $pasivoNoCorrientes= Sity::getDataParaBalanceGeneral($pcontable_id, 2, 0);
        //dd($pasivoNoCorrientes);        
 
        $patrimonios= Sity::getDataParaBalanceGeneral($pcontable_id, 3, Null);
        //dd($patrimonios); 
     
        //calcula el total de cada uno de los tipos de cuentas
        $total_activoCorrientes= 0;
        $total_activoNoCorrientes= 0;
        $total_pasivoCorrientes= 0;
        $total_pasivoNoCorrientes= 0;
        $total_patrimonios= 0;

        foreach($activoCorrientes as $activoCorriente) {
          $total_activoCorrientes += $activoCorriente['saldo_debito'];
        }
 
        foreach($activoNoCorrientes as $activoNoCorriente) {
          $total_activoNoCorrientes += $activoNoCorriente['saldo_debito'];
        }

        foreach($pasivoCorrientes as $pasivoCorriente) {
           $total_pasivoCorrientes += $pasivoCorriente['saldo_credito'];
        }

        foreach($pasivoNoCorrientes as $pasivoNoCorriente) {
          $total_pasivoNoCorrientes += $pasivoNoCorriente['saldo_credito'];
        }

        foreach($patrimonios as $patrimonio) {
          $total_patrimonios += $patrimonio['saldo_credito'];
        }
        // dd($total_activoCorrientes, $total_activoNoCorrientes, $total_pasivoCorrientes, $total_pasivoNoCorrientes, $total_patrimonios);        

        $totalActivos= $total_activoCorrientes+$total_activoNoCorrientes;
        $totalPasivos= $total_pasivoCorrientes+$total_pasivoNoCorrientes;
        $totalPasivoPatrimonio= $totalPasivos+$total_patrimonios;       

        $total_activoCorrientes=  $total_activoCorrientes;
        $total_activoNoCorrientes= $total_activoNoCorrientes;
        $total_pasivoCorrientes=  $total_pasivoCorrientes;
        $total_pasivoNoCorrientes=  $total_pasivoNoCorrientes;
        $total_patrimonio=  $total_patrimonios;

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
                    ->with('total_patrimonio', $total_patrimonio)                    
                    
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
        
        $datos= Ctmayore::where('pcontable_id', $periodo)
                       ->where('cuenta', $cuenta)
                       ->get();
        //dd($datos->toArray());        
        
        $data=array();    
        $i=1;
        
        foreach ($datos as $dato) {
            if ($dato->tipo==1 || $dato->tipo==6) {  
                if ($i==1) {
                    $saldo= $dato->debito-$dato->credito;
                    $datas[$i]['fecha']= $dato->fecha;
                    $datas[$i]['codigo']= $dato->codigo;
                    //$datas[$i]['nombre']= Catalogo::find($dato->cuenta)->nombre;
                    $datas[$i]['detalle']= $dato->detalle;
                    $datas[$i]['ref']= "";
                    $datas[$i]['debito']= $dato->debito;
                    $datas[$i]['credito']= $dato->credito;
                    $datas[$i]['saldo']=  $saldo;
                    
                } else {
                    $saldo= ($dato->debito-$dato->credito)+$saldo;
                    $datas[$i]['fecha']= $dato->fecha;
                    $datas[$i]['codigo']= $dato->codigo;
                    //$datas[$i]['nombre']= Catalogo::find($dato->cuenta)->nombre;
                    $datas[$i]['detalle']= $dato->detalle;
                    $datas[$i]['ref']= "";
                    $datas[$i]['debito']= $dato->debito;
                    $datas[$i]['credito']= $dato->credito;
                    $datas[$i]['saldo']=  $saldo;
                }       


            } elseif ($dato->tipo==2 || $dato->tipo==3 || $dato->tipo==4) {  
                if ($i==1) {
                    $saldo= $dato->credito-$dato->debito;
                    $datas[$i]['fecha']= $dato->fecha;
                    $datas[$i]['codigo']= $dato->codigo;
                    //$datas[$i]['nombre']= Catalogo::find($dato->cuenta)->nombre;
                    $datas[$i]['detalle']= $dato->detalle;
                    $datas[$i]['ref']= "";
                    $datas[$i]['debito']= $dato->debito;
                    $datas[$i]['credito']= $dato->credito;
                    $datas[$i]['saldo']=  $saldo;
                    
                } else {
                    $saldo= ($dato->credito-$dato->debito)+$saldo;
                    $datas[$i]['fecha']= $dato->fecha;
                    $datas[$i]['codigo']= $dato->codigo;
                    //$datas[$i]['nombre']= Catalogo::find($dato->cuenta)->nombre;
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
    * Despliega la hoja de trabajo final de un determinado periodo
    ************************************************************************************/ 
    public function hojadetrabajo($periodo) {
        $datos = Ht::where('pcontable_id', $periodo)->get();
        //dd($datos->toArray());
        
        $utilidad= $datos->sum('er_credito')-$datos->sum('er_debito'); 
        //dd($utilidad);

        return \View::make('contabilidad.hojadetrabajos.hojadetrabajo')
                    ->with('datos', $datos)
                    ->with('total_bp_debito', $datos->sum('bp_debito'))                  
                    ->with('total_bp_credito', $datos->sum('bp_credito')) 
                    ->with('total_aj_debito', $datos->sum('aj_debito'))                  
                    ->with('total_aj_credito', $datos->sum('aj_credito')) 
                    ->with('total_ba_debito', $datos->sum('ba_debito'))                  
                    ->with('total_ba_credito', $datos->sum('ba_credito')) 
                    ->with('total_er_debito', $datos->sum('er_debito'))                  
                    ->with('total_er_credito', $datos->sum('er_credito')) 
                    ->with('total_bg_debito', $datos->sum('bg_debito'))                  
                    ->with('total_bg_credito', $datos->sum('bg_credito')) 
                    ->with('utilidad', $utilidad) 
                    ->with('periodo', Pcontable::find($periodo)->periodo);
    } 

    /***********************************************************************************
    * Cierra definitivamente un determinado periodo contable
    ************************************************************************************/ 
    public function cierraPeriodo($pcontable_id, $periodo) {
        
        // inicializa las cuentas permanentes en periodo posterior
        Sity::inicializaCuentasPerm($pcontable_id);
        //dd('aqui');
        // calcula la utilidad del periodo contable antes de cerrarlo y se la pasa al periodo posterior
        Sity::pasarUtilidad($pcontable_id, $periodo);

        // almacena datos del periodo antes de cerrarlo y las almacena en la tabla Hts (hoja de trabajo historica)
        Sity::migraDatosHts($pcontable_id);

        // cierra todas la cuentas nominales o temporales por finalizacion de periodo contable
        Sity::cierraCuentasTemp($pcontable_id);

        // cierra el periodo contable
        $periodo= Pcontable::find($pcontable_id);
        $periodo->cerrado = 1;
        $periodo->f_cierre = Carbon::today();
        $periodo->save();
         
        // migra los datos de ctmayores a la tabla de datos historicos ctmayorehi y 
        // posteriormente los borra de la tabla ctmayores
        Sity::migraDatosCtmayorehis($pcontable_id);
        
        // migra los datos de ctdiarios a la tabla de datos historicos ctdiariohis y 
        // posteriormente los borra de la tabla ctdiarios
        Sity::migraDatosCtdiariohis($pcontable_id);

        // registra en bitacoras
        Sity::RegistrarEnBitacora(17, 'pcontables', $pcontable_id, $periodo);
        Session::flash('success', 'Periodo '.$periodo->periodo.' ha sido cerrado permanentemente!');
        return Redirect::route('pcontables.index');
    }
} // fin de controller