<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Ctdiario;
use App\Ctdiariohi;
use App\Bitacora;
use App\Pcontable;


class CtdiariosController extends Controller {
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
  ************************************************************************************/	
	public function show($pcontable_id)
	{
    //Obtiene todos los asientos del Libro Ctdiario.
    $datos = Ctdiario::where('pcontable_id', $pcontable_id)->get();
    //dd($datos->toArray());
		
    $total_debito= Ctdiario::where('pcontable_id', $pcontable_id)->sum('debito');
    $total_credito= Ctdiario::where('pcontable_id', $pcontable_id)->sum('credito');
    //dd($total_debito, $total_credito);

    $periodo= Pcontable::find($pcontable_id);
    $periodo= $periodo->periodo;
    //dd($periodo->periodo);
		
    foreach ($datos as $dato) {
      if ($dato->fecha) {
        $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
      }        
    }
    
    return \View::make('contabilidad.ctdiarios.show')
                ->with('total_debito', $total_debito)
                ->with('total_credito', $total_credito)
                ->with('periodo', $periodo)
                ->with('datos', $datos);
	}	

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
  ************************************************************************************/ 
  public function diarioFinal($pcontable_id)
  {
    //Obtiene todos los asientos del Libro Ctdiario.
    $datos = Ctdiariohi::where('pcontable_id', $pcontable_id)->get();
    //dd($datos->toArray());
    
    Carbon::setLocale('es');
    foreach ($datos as $dato) {
      if ($dato->fecha) {
        $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
      }        
    }
    
    $total_debito= Ctdiariohi::where('pcontable_id', $pcontable_id)->sum('debito');
    $total_credito= Ctdiariohi::where('pcontable_id', $pcontable_id)->sum('credito');
    //dd($total_debito, $total_credito);

    $periodo= Pcontable::find($pcontable_id);
    $periodo= $periodo->periodo;
    //dd($periodo->periodo);
    
    return \View::make('contabilidad.ctdiarios.index')
                ->with('total_debito', $total_debito)
                ->with('total_credito', $total_credito)
                ->with('periodo', $periodo)
                ->with('datos', $datos);
  } 
}