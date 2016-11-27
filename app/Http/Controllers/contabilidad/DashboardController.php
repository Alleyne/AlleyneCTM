<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
//use Charts;
use App\Ctdasm;
use App\Pcontable;
use App\Un;
use DB;

class DashboardController extends Controller
{
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega todos los pagos que pertenecen a una determinada Unidad.
     ************************************************************************************/	
	public function graph_1($pcontable_id)
	{
	    
        // optiene todos los datos necesarios para la grafica de morosos
        $ctdasm= Ctdasm::All();
        $uns= Un::where('activa', 1)->get();

        // agrega a la colleccion $uns un nuevo elemento llamado "deuda" el cual almacena el total de la deuda por unidad
        $i=0;
        foreach ($uns as $un) {
            $ctdasm= Ctdasm::where('un_id', $un->id)->get();
            $importe= $ctdasm->where('pagada', 0)->sum('importe');
            $recargo= $ctdasm->where('recargo_siono', 1)->where('recargo_pagado', 0)->sum('recargo');
            $extra= $ctdasm->where('extra_siono', 1)->where('extra_pagada', 0)->sum('extra');
            
            $uns[$i]["deuda"] = $importe + $recargo + $extra;
            $i++;
        }

        // ordena de forma descenciente la colleccion
        $uns= $uns->where('deuda', '>', 0)->sortByDesc('deuda');
        //dd($uns->toArray()); 

        foreach ($uns as $un) {
            $ctdasm= Ctdasm::where('un_id', $un->id)->get();
            $importe= $ctdasm->where('pagada', 0)->sum('importe');
            $recargo= $ctdasm->where('recargo_siono', 1)->where('recargo_pagado', 0)->sum('recargo');
            $extra= $ctdasm->where('extra_siono', 1)->where('extra_pagada', 0)->sum('extra');
            
            $data_1[]= $importe;
            $data_2[]= $recargo;
            $data_3[]= $extra;
            
            $propietario= $un->props()->where('encargado', 1)->first();
            $propietario= $propietario->user->nombre_completo; 
            $categorias[]= $propietario.' '.$un->codigo;
        }
        //dd($uns->toArray()); 
        
        // formatea los arrays
        $data_1 = implode(", ", $data_1);
        $data_2 = implode(", ", $data_2);
        $data_3 = implode(", ", $data_3);
        $categorias = '"'.implode('", "', $categorias).'"';    

        // obtiene los doce ultimos periodos contables registrados
        $periodos= Pcontable::distinct('id')->orderBy('id')->take(12)->get();
        //dd($periodos->toArray());

        foreach ($periodos as $periodo) {
            $ctdasm= Ctdasm::where('pcontable_id', $periodo->id)->get();

            //----------------------------------------------------------------------
            // calcula el total ingresos esperado por cada periodo contable
            //----------------------------------------------------------------------
            // calcula el total de descuentos otorgados por pagos anticipados
            $totalEspDescuentos = $ctdasm->where('descuento_siono', 1)->sum('descuento');  
            
           // calcula el total de ingresos por cuotas regulares
            $totalEspRegulares = $ctdasm->sum('importe') - $totalEspDescuentos;   

            // calcula el total de recargos
            $totalEspRecargos = $ctdasm->where('recargo_siono', 1)->sum('recargo');  
            
            // calcula el total de cuotas extraordinarias
            $totalEspExtraordinarias = $ctdasm->where('extra_siono', 1)->sum('extra');  
            
            $totalIngresoEsperado= $totalEspRegulares + $totalEspRecargos + $totalEspExtraordinarias;
            //dd($totalEspRegulares, $totalEspRecargos, $totalEspExtraordinarias);

            //----------------------------------------------------------------------
            //calula el total recibido a la fecha
            //----------------------------------------------------------------------
            // calcula el total de ingresos por coutas regulares
            $totalPagRegulares= $ctdasm->where('pagada', 1)->sum('importe') - $totalEspDescuentos;    
            
            // calcula el total de recargos
            $totalPagRecargos= $ctdasm->where('recargo_pagado', 1)->sum('recargo');  
            
            // calcula el total de cuotas extraordinarias
            $totalPagExtraordinarias= $ctdasm->where('extra_pagada', 1)->sum('extra');  

            $totalIngresoPagados= $totalPagRegulares + $totalPagRecargos + $totalPagExtraordinarias;
            
            $pdo[]= $periodo->periodo;            
            $pagRegulares[]= $totalPagRegulares;
            $pagRecargos[]= $totalPagRecargos;
            $pagExtraordinarias[]= $totalPagExtraordinarias;
            //dd($pagRegulares, $pagRecargos, $pagExtraordinarias);
            
            $totalIngresoPorCobrar[]= $totalIngresoEsperado - ($totalPagRegulares + $totalPagRecargos + $totalPagExtraordinarias);
        }
        //dd($pdo, $totalRegulares, $totalDescuentos, $totalRecargos, $totalExtraordinarias);
        
        $pdo = '"'.implode('", "', $pdo).'"';  
        $pagRegulares = implode(", ", $pagRegulares);
        $pagRecargos = implode(", ", $pagRecargos);
        $pagExtraordinarias = implode(", ", $pagExtraordinarias);  
        $totalIngresoPorCobrar = implode(", ", $totalIngresoPorCobrar); 
        
        return view('contabilidad.dashboard.graph_1', [
            											'data_1' => $data_1,
                                                        'data_2' => $data_2,
                                                        'data_3' => $data_3,
                                                        'categorias' => $categorias,                                                      
                                                        'pdo' => $pdo,
                                                        'pagRegulares' => $pagRegulares,
                                                        'pagRecargos' => $pagRecargos,
                                                        'pagExtraordinarias' => $pagExtraordinarias,
                                                        'totalIngresoPorCobrar' => $totalIngresoPorCobrar                                                        
                                                      ]);
    }	
} // end of class