<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
//use Charts;
use App\Ctdasm;
use App\Pcontable;
use App\Un;
use DB; 
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega todos los pagos que pertenecen a una determinada Unidad.
   ************************************************************************************/  
  public function graph_1($pcontable_id) {
    /*
    |--------------------------------------------------------------------------
    | Procesa todos los datos necesarios para la grafica de morosos
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------------
    | Procesa todos los datos necesarios para la grafica Ingresos vs pagos recibidos
    |--------------------------------------------------------------------------------
    */
      // obtiene los doce ultimos periodos contables registrados
      $periodos= Pcontable::orderBy('id', 'desc')->take(12)->get();
      $periodos= $periodos->sortBy('id');
      //dd($periodos->toArray());

      foreach ($periodos as $periodo) {
        $f_inicio= Carbon::parse($periodo->fecha);
        $f_final= Carbon::parse($periodo->fecha)->endOfMonth()->toDateString();
        
        $ctdasm= Ctdasm::where('pcontable_id', $periodo->id)->get();
        $recargos= Ctdasm::whereDate('f_vencimiento', '>=', $f_inicio)->whereDate('f_vencimiento', '<=', $f_final)->get();
        //dd($recargos);
        //----------------------------------------------------------------------
        // calcula el total ingresos esperado por cada periodo contable
        //----------------------------------------------------------------------
          // calcula el total de descuentos otorgados por pagos anticipados
          $totalEspDescuentos = $ctdasm->where('descuento_siono', 1)->sum('descuento');  

         // calcula el total de ingresos por cuotas regulares sin descuento incluido
          $totalEspRegularesSD = $ctdasm->sum('importe');   
         
         // calcula el total de ingresos por cuotas regulares con descuento incluido
          $totalEspRegularesCD = $ctdasm->sum('importe') - $totalEspDescuentos;   

          // calcula el total de recargos
          $totalEspRecargos = $recargos->where('recargo_siono', 1)->sum('recargo');  
          
          // calcula el total de cuotas extraordinarias
          $totalEspExtraordinarias = $ctdasm->where('extra_siono', 1)->sum('extra');  
          
          // calcula el total de ingresos esperado con descuento por cobrar
          $totalIngresoEsperadoCD= $totalEspRegularesCD + $totalEspRecargos + $totalEspExtraordinarias;
          
          // calcula el total de ingresos esperado sin descuento
          //$totalIngresoEsperadoSD= $totalEspRegularesSD + $totalEspRecargos + $totalEspExtraordinarias;
          $totalIngresoEsperadoSD= $totalEspRegularesSD;

        //----------------------------------------------------------------------
        // calula el total de pagos recibidos a la fecha
        //----------------------------------------------------------------------
          // calcula el total de ingresos por coutas regulares
          $totalPagRegulares= $ctdasm->where('pagada', 1)->sum('importe') - $totalEspDescuentos;    
          
          // calcula el total de recargos
          $totalPagRecargos= $recargos->where('recargo_pagado', 1)->sum('recargo');  
          
          // calcula el total de cuotas extraordinarias
          $totalPagExtraordinarias= $ctdasm->where('extra_pagada', 1)->sum('extra');  

          $totalIngresoPagados= $totalPagRegulares + $totalPagRecargos + $totalPagExtraordinarias;
          
          $pdo[]= $periodo->periodo;            
          $pagRegulares[]= $totalPagRegulares;
          $descuentos[]= $totalEspDescuentos;
          $pagRecargos[]= $totalPagRecargos;
          $pagExtraordinarias[]= $totalPagExtraordinarias;
          $totalIngresoPorCobrarCD[]= $totalIngresoEsperadoCD - ($totalPagRegulares + $totalPagRecargos + $totalPagExtraordinarias);
          $totalIngresoPorCobrarSD[]= $totalIngresoEsperadoSD;
      }
      
      // formatea los datos antes de ser enviados a la grafica
      $pdo = '"'.implode('", "', $pdo).'"';  
      $pagRegulares = implode(", ", $pagRegulares);
      $descuentos = implode(", ", $descuentos);
      $pagRecargos = implode(", ", $pagRecargos);
      $pagExtraordinarias = implode(", ", $pagExtraordinarias);  
      $totalIngresoPorCobrarCD = implode(", ", $totalIngresoPorCobrarCD); 
      $totalIngresoPorCobrarSD = implode(", ", $totalIngresoPorCobrarSD); 
    
    /*
    |--------------------------------------------------------------------------------
    | 
    |--------------------------------------------------------------------------------
    */


    return view('contabilidad.dashboard.graph_1', [
                              'data_1' => $data_1,
                              'data_2' => $data_2,
                              'data_3' => $data_3,
                              'categorias' => $categorias,                                                      
                              'pdo' => $pdo,
                              'pagRegulares' => $pagRegulares,
                              'descuentos' => $descuentos,
                              'pagRecargos' => $pagRecargos,
                              'pagExtraordinarias' => $pagExtraordinarias,
                              'totalIngresoPorCobrar' => $totalIngresoPorCobrarCD,                                                        
                              'totalIngreso' => $totalIngresoPorCobrarSD   
                            ]);
  } 
} // end of class