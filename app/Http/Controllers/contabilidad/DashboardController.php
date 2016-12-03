<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use App\library\Hojat;
use Carbon\Carbon;

use App\Ctdasm;
use App\Pcontable;
use App\Un;
use App\Ctmayore;
use App\Catalogo;

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

      if ($uns->count()) {
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
      
      } else {
        $data_1 = Null;
        $data_2 = Null;
        $data_3 = Null;
        $categorias = Null; 
      }

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
        //$f_inicio= Carbon::parse($periodo->fecha);
        //$f_final= Carbon::parse($periodo->fecha)->endOfMonth();
        
        $f_inicio= new Carbon($periodo->fecha);
        $f_final= new Carbon($periodo->fecha);
        $f_final= $f_final->endOfMonth();

        $ctdasm= Ctdasm::where('pcontable_id', $periodo->id)->get();
        $recargos= Ctdasm::whereBetween('f_vencimiento',[$f_inicio, $f_final])->get();
        
        //----------------------------------------------------------------------
        // calcula el total ingresos esperado por cada periodo contable
        //----------------------------------------------------------------------
          // calcula el total de descuentos otorgados por pagos anticipados
          $_totalDescuentos = $ctdasm->where('descuento_siono', 1)->sum('descuento');  

          // calcula el total de ingresos por cuotas regulares sin descuento incluido
          $_totalEspRegularesSD = $ctdasm->sum('importe');   
   
          // calcula el total de ingresos por cuotas regulares con descuento incluido
          $_totalEspRegularesCD = $_totalEspRegularesSD - $_totalDescuentos;   

          // calcula el total de recargos
          $_totalEspRecargos = $recargos->where('recargo_siono', 1)->sum('recargo');

          // calcula el total de cuotas extraordinarias
          $_totalEspExtraordinarias = $ctdasm->where('extra_siono', 1)->sum('extra');  
          
          
          // calcula el total de ingresos esperado sin descuento
          $_totalIngresoEsperadoSD= $_totalEspRegularesSD + $_totalEspRecargos + $_totalEspExtraordinarias;

          // calcula el total de ingresos esperado con descuento
          $_totalIngresoEsperadoCD= $_totalEspRegularesCD + $_totalEspRecargos + $_totalEspExtraordinarias;

        //----------------------------------------------------------------------
        // calcula el total de pagos recibidos a la fecha
        //----------------------------------------------------------------------
          // calcula el total de ingresos por coutas regulares
          $_totalPagRegulares= $ctdasm->where('pagada', 1)->sum('importe') - $_totalDescuentos;    
          
          // calcula el total de recargos
          $_totalPagRecargos= $recargos->where('recargo_pagado', 1)->sum('recargo');  
          
          // calcula el total de cuotas extraordinarias
          $_totalPagExtraordinarias= $ctdasm->where('extra_pagada', 1)->sum('extra');  

          
          // calcula el total de ingresos pagados
          $_totalIngresoPagados= $_totalPagRegulares + $_totalPagRecargos + $_totalPagExtraordinarias;
          
          //----------------------------------------------------------------------
          // calcula el total de gastos efectuados a la fecha
          //----------------------------------------------------------------------          
          // crea una colleccion con todas las cuenta de gastos 
          $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
          $_totalGastos= $ctmayores->sum('debito') - $ctmayores->sum('credito');
  
          // almacena los datos en arreglos por periodo contable
          $pdo[]= $periodo->periodo;            
          $totalDescuentos[]= $_totalDescuentos;          
          
          $totalEspRegularesSD[]= $_totalEspRegularesSD;
          $totalEspRegularesCD[]= $_totalEspRegularesCD;
          $totalEspRecargos[]= $_totalEspRecargos;
          $totalEspExtraordinarias[]= $_totalEspExtraordinarias;          
          
          $totalPagRegulares[]= $_totalPagRegulares;
          $totalPagRecargos[]= $_totalPagRecargos;
          $totalPagExtraordinarias[]= $_totalPagExtraordinarias;
          
          $totalIngresoEsperadoSD[]= $_totalIngresoEsperadoSD;
          $totalIngresoEsperadoCD[]= $_totalIngresoEsperadoCD;
          
          $totalIngresoPorCobrarCD[]= $_totalIngresoEsperadoCD - ($_totalPagRegulares + $_totalPagRecargos + $_totalPagExtraordinarias);
          
          $totalGastos[]= $_totalGastos;
      }

      // formatea los datos antes de ser enviados a la grafica
      $pdo = '"'.implode('", "', $pdo).'"';  
      $descuentos = implode(", ", $totalDescuentos);      
      
      $espRegularesSD = implode(", ", $totalEspRegularesSD);
      $espRegularesCD = implode(", ", $totalEspRegularesCD);
      $espRecargos = implode(", ", $totalEspRecargos);
      $espExtraordinarias = implode(", ", $totalEspExtraordinarias);
      
      $pagRegulares = implode(", ", $totalPagRegulares);
      $pagRecargos = implode(", ", $totalPagRecargos);
      $pagExtraordinarias = implode(", ", $totalPagExtraordinarias);  
      
      $totalIngresoEsperadoSD = implode(", ", $totalIngresoEsperadoSD); 
      $totalIngresoEsperadoCD = implode(", ", $totalIngresoEsperadoCD); 
      
      $totalIngresoPorCobrarCD = implode(", ", $totalIngresoPorCobrarCD); 
      
      $totalGastos = implode(", ", $totalGastos);     

    /*
    |------------------------------------------------------------------------------------------
    | Procesa los datos necesarios para la grafica de Ingreso vs Gastos para el periodo actual
    |------------------------------------------------------------------------------------------
    */
    // encuentra el periodo mas antiguo abierto
    $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first()->id;
    
    // calcula el total de ingresos del periodo
    $ingresos= Ctmayore::where('pcontable_id', $periodo)->where('tipo', 4)->get();
    $totalIngresos= $ingresos->sum('credito') - $ingresos->sum('debito');
    //dd($totalIngresos);

    // crea una colleccion con todas las cuenta de gastos 
    $ctmayores= Ctmayore::where('pcontable_id', $periodo)->where('tipo', 6)->get();
    
    // calcula el total de itbms del periodo    
    $itbms= $ctmayores->where('cuenta', 15);
    $totalItbms= $itbms->sum('debito') - $itbms->sum('credito');
    // dd($totalItbms);
    
    // calcula el total de gastos del periodo excluyendo la cuenta de itbms
    $gastos= $ctmayores->where('cuenta','!=', 15);
    
    // excluye todos los registro que el campo debito y credito sea igual a cero
    $gastos = $gastos->reject(function($gasto) {
      return $gasto->debito == "0.00" && $gasto->credito == "0.00"; 
    });
    
    $gastostotales= $gastos->sum('debito') - $gastos->sum('credito');
    //dd($totalGastos);
    
    $data="";
    foreach ($gastos as $gasto) {
      $data = $data.'{name: "'.Catalogo::find($gasto->cuenta)->nombre.'", y: '.$gasto->debito.'},';
    }
    $data= rtrim($data, ',');
    // dd($data);
    
    /*
    |--------------------------------------------------------------------------------
    | Procesa los datos necesarios para la grafica de Utilidad Ingresos vs Gastos para el periodo actual
    |--------------------------------------------------------------------------------
    */
    $ER_totalGastos= $gastostotales + $totalItbms;
    $ER_totalIngresos= $totalIngresos - ($gastostotales + $totalItbms);
    //dd($totalIngresos, $totalGastos);

    return view('contabilidad.dashboard.graph_1', [
                                                    'data_1' => $data_1,
                                                    'data_2' => $data_2,
                                                    'data_3' => $data_3,
                                                    'categorias' => $categorias,                                                      
                                                    
                                                    'pdo' => $pdo,
                                                    'descuentos' => $descuentos,                              

                                                    'espRegularesSD' => $espRegularesSD,
                                                    'espRegularesCD' => $espRegularesCD,
                                                    'espRecargos' => $espRecargos,
                                                    'espExtraordinarias' => $espExtraordinarias,

                                                    'pagRegulares' => $pagRegulares,
                                                    'pagRecargos' => $pagRecargos,
                                                    'pagExtraordinarias' => $pagExtraordinarias,
                                                   
                                                    'totalIngresoEsperadoSD' => $totalIngresoEsperadoSD,
                                                    'totalIngresoEsperadoCD' => $totalIngresoEsperadoCD,
                                                    
                                                    'totalIngresoPorCobrarCD' => $totalIngresoPorCobrarCD,                                  
                                                    
                                                    'totalGastos' => $totalGastos,                                  

                                                    'ER_totalIngresos' => $ER_totalIngresos,
                                                    'ER_totalGastos' => $ER_totalGastos,
                                                    'itbms' => $totalItbms,
                                                    'data' => $data
                                                  ]);
  } 
} // end of class