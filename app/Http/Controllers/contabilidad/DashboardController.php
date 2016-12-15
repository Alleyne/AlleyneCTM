<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use App\library\Graph;
use App\library\Grupo;
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
   * Procesa la data necesaria para desplegar la grafica de propietarios mosoros en Front end
   ************************************************************************************/  
  public function historico() {
    // encuentra la data para la grafica de morosos
    $dataMorosos= Graph::getDataGraphMorosos();
    //dd($dataMorosos);

    // encuentra la data para la grafica de gastos por periodo contable    
    $dataGastos= Graph::getDataGraphGastos();
    //dd($dataGastos);

    $dataGastosTotales= Graph::getDataGraphGastosTotales();
    //dd($dataGastosTotales);

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
          $_totalDescuentos= $ctdasm->where('descuento_siono', 1)->sum('descuento');  

          // calcula el total de ingresos por cuotas regulares sin descuento incluido
          $_totalEspRegularesSD= $ctdasm->sum('importe');   
   
          // calcula el total de ingresos por cuotas regulares con descuento incluido
          $_totalEspRegularesCD= $_totalEspRegularesSD - $_totalDescuentos;   

          // calcula el total de recargos
          $_totalEspRecargos= $recargos->where('recargo_siono', 1)->sum('recargo');

          // calcula el total de cuotas extraordinarias
          $_totalEspExtraordinarias= $ctdasm->where('extra_siono', 1)->sum('extra');  
          
          
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
          //$ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
          $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
          $_totalGastos= $ctmayores->sum('debito') - $ctmayores->sum('credito');

          // almacena los datos en arreglos por periodo contable
          $pdo[]= $periodo->periodo;            
          $totalDescuentos[]= $_totalDescuentos;          
          
          $totalEspRegularesSD[]= $_totalEspRegularesSD;
          $totalEspRegularesCD[]= $_totalEspRegularesCD;
          $totalEspRecargos[]= $_totalEspRecargos;
          $totalEspExtraordinarias[]= $_totalEspExtraordinarias;          
          
          $totalIngresoEsperadoSD[]= $_totalIngresoEsperadoSD;
          $totalIngresoEsperadoCD[]= $_totalIngresoEsperadoCD;         

          $totalPagRegulares[]= $_totalPagRegulares;
          $totalPagRecargos[]= $_totalPagRecargos;
          $totalPagExtraordinarias[]= $_totalPagExtraordinarias;
          
          $totalPagado[]= $_totalPagRegulares + $_totalPagRecargos + $_totalPagExtraordinarias;

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
      
      $totalIngresoEsperadoSD = implode(", ", $totalIngresoEsperadoSD); 
      $totalIngresoEsperadoCD = implode(", ", $totalIngresoEsperadoCD);       
      
      $pagRegulares = implode(", ", $totalPagRegulares);
      $pagRecargos = implode(", ", $totalPagRecargos);
      $pagExtraordinarias = implode(", ", $totalPagExtraordinarias);  
      
      $totalPagado = implode(", ", $totalPagado); 

      $totalIngresoPorCobrarCD = implode(", ", $totalIngresoPorCobrarCD); 
      
      $totalGastos = implode(", ", $totalGastos);     

      $viewData= [
                  'dataMorosos' => $dataMorosos,
                  'dataGastos' => $dataGastos,                  
                  'dataGastosTotales' => $dataGastosTotales, 

                  'pdo' => $pdo,
                  'descuentos' => $descuentos,                              

                  'espRegularesSD' => $espRegularesSD,
                  'espRegularesCD' => $espRegularesCD,
                  'espRecargos' => $espRecargos,
                  'espExtraordinarias' => $espExtraordinarias,
                  
                  'totalIngresoEsperadoSD' => $totalIngresoEsperadoSD,
                  'totalIngresoEsperadoCD' => $totalIngresoEsperadoCD,
                  
                  'pagRegulares' => $pagRegulares,
                  'pagRecargos' => $pagRecargos,
                  'pagExtraordinarias' => $pagExtraordinarias,
                  
                  'totalPagado' => $totalPagado,                                  
                  
                  'totalIngresoPorCobrarCD' => $totalIngresoPorCobrarCD,                                  
                  
                  'totalGastos' => $totalGastos                                  
                 ];
    
      if (Grupo::esAdmin()) {
        return view('contabilidad.dashboard.historico', $viewData); 
      } elseif (Grupo::esPropietario() || Grupo::esAdminDeBloque()) {
        return view('contabilidad.dashboard.historicoFrontend', $viewData); 
      }
  } // end function

  /*************************************************************************************
   * Procesa la data necesaria para desplegar la grafica de propietarios mosoros en Front end
   ************************************************************************************/  
  public function vigente() {
    // encuentra la data para la grafica de morosos
    $data= Graph::getDataGraphMorosos();
    //dd($data);

    /*
    |--------------------------------------------------------------------------------
    | Procesa todos los datos necesarios para la grafica Ingresos vs pagos recibidos
    |--------------------------------------------------------------------------------
    */
      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();

      $f_inicio= new Carbon($periodo->fecha);
      $f_final= new Carbon($periodo->fecha);
      $f_final= $f_final->endOfMonth();

      //$ctdasm= Ctdasm::where('pcontable_id', $periodo->id)->get();
      $ctdasm= Ctdasm::All();
      $recargos= Ctdasm::whereBetween('f_vencimiento',[$f_inicio, $f_final])->get();
      
      //----------------------------------------------------------------------
      // calcula el total ingresos esperado para el periodo vigente
      //----------------------------------------------------------------------
        // calcula el total de descuentos otorgados por pagos anticipados
        $_totalDescuentos = $ctdasm->where('descuento_siono', 1)->sum('descuento');  

        // calcula el total de ingresos por cuotas regulares sin descuento incluido
        //$_totalEspRegularesSD = $ctdasm->sum('importe');   
 
        // calcula el total de ingresos por cuotas regulares con descuento incluido
        //$_totalEspRegularesCD = $_totalEspRegularesSD - $_totalDescuentos;   

        // calcula el total de recargos
        //$_totalEspRecargos = $recargos->where('recargo_siono', 1)->sum('recargo');

        // calcula el total de cuotas extraordinarias
        //$_totalEspExtraordinarias = $ctdasm->where('extra_siono', 1)->sum('extra');  
        
        
        // calcula el total de ingresos esperado sin descuento
        //$_totalIngresoEsperadoSD= $_totalEspRegularesSD + $_totalEspRecargos + $_totalEspExtraordinarias;

        // calcula el total de ingresos esperado con descuento
        //$_totalIngresoEsperadoCD= $_totalEspRegularesCD + $_totalEspRecargos + $_totalEspExtraordinarias;

      //----------------------------------------------------------------------
      // calcula el total de pagos recibidos a la fecha
      //----------------------------------------------------------------------
        // calcula el total de ingresos por coutas regulares
        $_totalPagRegulares= $ctdasm->where('pagada', 1)->sum('importe') - $_totalDescuentos;    
        
        // calcula el total de recargos
        $_totalPagRecargos= $recargos->where('recargo_pagado', 1)->sum('recargo');  
        
        // calcula el total de cuotas extraordinarias
        $_totalPagExtraordinarias= $ctdasm->where('extra_pagada', 1)->sum('extra');  

        
        // calcula el total de ingresos pagados por los propietarios
        $_totalIngresoPagados= $_totalPagRegulares + $_totalPagRecargos + $_totalPagExtraordinarias;
        

        //----------------------------------------------------------------------
        // calcula el total de gastos efectuados a la fecha
        //----------------------------------------------------------------------          
        // crea una colleccion con todas las cuenta de gastos 
        $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
        $_totalGastos= $ctmayores->sum('debito') - $ctmayores->sum('credito');
  
    /*
    |------------------------------------------------------------------------------------------
    | Procesa los datos necesarios para la grafica de Ingreso vs Gastos para el periodo actual
    |------------------------------------------------------------------------------------------
    */
    
    // calcula el total de ingresos del periodo
    $ingresos= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 4)->get();
    $totalIngresos= $ingresos->sum('credito') - $ingresos->sum('debito');
    //dd($totalIngresos);

    // crea una colleccion con todas las cuenta de gastos 
    $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
    
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
    
    $datos="";
    foreach ($gastos as $gasto) {
      $datos = $datos.'{name: "'.Catalogo::find($gasto->cuenta)->nombre.'", y: '.$gasto->debito.'},';
    }
    $gastos= rtrim($datos, ',');
    // dd($gastos);
    
    /*
    |--------------------------------------------------------------------------------
    | Procesa los datos necesarios para la grafica de Utilidad Ingresos vs Gastos para el periodo actual
    |--------------------------------------------------------------------------------
    */
    $ER_totalGastos= $gastostotales + $totalItbms;
    $ER_totalIngresos= $totalIngresos - ($gastostotales + $totalItbms);
    //dd($totalIngresos, $totalGastos);

    // total de ingresos disponibles para utilizar
    $totalIngresosDisponible= $_totalIngresoPagados - $gastostotales;  
    //dd($totalIngresosDisponible);
    
    $viewData=[
                'pdo' => $periodo->perido,
                'data' => $data,
                'descuentos' => $_totalDescuentos,                              

                //'espRegularesSD' => $_totalEspRegularesSD,
                //'espRegularesCD' => $_totalEspRegularesCD,
                //'espRecargos' => $_totalEspRecargos,
                //'espExtraordinarias' => $_totalEspExtraordinarias,
                
                //'totalIngresoEsperadoSD' => $_totalEspRegularesSD,
                //'totalIngresoEsperadoCD' => $_totalEspRegularesCD,
                
                'pagRegulares' => $_totalPagRegulares,
                'pagRecargos' => $_totalPagRecargos,
                'pagExtraordinarias' => $_totalPagExtraordinarias,
                
                'totalPagado' => $_totalIngresoPagados,                                  
                'totalGastos' => $gastostotales,                                  
                'totalIngresosDisponible' => $totalIngresosDisponible, 
                
                'ER_totalIngresos' => $ER_totalIngresos,
                'ER_totalGastos' => $ER_totalGastos,
                'itbms' => $totalItbms,
                'gastos' => $gastos
              ]; 
  
    if (Grupo::esAdmin()) {
      return view('contabilidad.dashboard.vigente', $viewData);  
    } elseif (Grupo::esPropietario() || Grupo::esAdminDeBloque()) {
      return view('contabilidad.dashboard.vigenteFrontend', $viewData); 
    }

  } // end function
} // end of class