<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Charts;
use App\Http\Requests;
use App\Ctdasm;
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
	    // Determina si la unidad tiene alguna facturacion pendiente por pagar
	    //$ingresos = Ctdasm::groupBy('pcontable_id')->get();
	    //dd($ingresos->toArray());  
	    
		//$ingresos = Ctdasm::groupBy('pcontable_id')
               // ->get();

//$ingresos =DB::table('ctdasms')->groupBy('pcontable_id')->sum('importe');


/*$ingresos= DB::table('ctdasms')->select('importe', DB::raw('count(*) as total'))->groupBy('pcontable_id') ->get();	    
dd($ingresos);*/	    

	    // Determina si la unidad tiene alguna facturacion pendiente por pagar
	    $pagados = Ctdasm::where('pcontable_id', $pcontable_id)
	                  ->where('pagada', 1)
	                  ->sum('importe');
	    //dd($pagados);
		
        $chart = Charts::new('line', 'highcharts')
            ->setTitle('Ingresos por cuotas de mantenimiento')
            ->setLabels(['First', 'Second', 'Third'])
            ->setValues([5,10,20])
            ->setDimensions(888,250)
            ->setResponsive(false);
        
        $chart2 = Charts::new('pie', 'highcharts')
            ->setTitle('Ingresos por cuotas de mantenimiento')
            ->setLabels(['First', 'Second', 'Third'])
            ->setValues([5,10,20])
            ->setDimensions(425,250)
            ->setResponsive(false);
        
        $chart3 = Charts::new('bar', 'highcharts')
            ->setTitle('Ingresos por cuotas de mantenimiento')
            ->setLabels(['First', 'Second', 'Third'])
            ->setValues([5,10,20])
            ->setDimensions(425,250)
            ->setResponsive(false);
        return view('contabilidad.dashboard.graph_1', ['chart' => $chart,
        												'chart2' => $chart2,
        												'chart3' => $chart3
        											  ]); 	
	}	
} // end of class