<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Cajachica;
use App\Dte_cajachica;
use App\Catalogo;

class Dte_cajachicasController extends Controller {
  
  public function __construct()
  {
   	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function show($cajachica_id)
	{
    $datos = Dte_cajachica::where('cajachica_id', $cajachica_id)->get();
    //dd($datos->toArray());		

    // encuentra los datos generales del encabezado de egreso de caja chica
    $cajachica= Cajachica::find($cajachica_id);
		//dd($cajachica->toArray());

		return view('contabilidad.dte_cajachicas.show')
				 ->with('cajachica', $cajachica)
         ->with('f_actual', Date::parse(Carbon::today())->toFormattedDateString())
         ->with('datos', $datos);     	
	}	
} 