<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session, DB;
use Validator;
use Carbon\Carbon;
use App\library\Sity;

use App\Pcontable;
use App\Bitacora;

class PcontablesController extends Controller {
    
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function index()
	{
        //Obtiene todos los Periodos contables.
        $datos = Pcontable::All();
        //dd($datos->toArray());
  		
  		return view('contabilidad.pcontables.index')->with('datos', $datos);     	
	}	

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/  
  public function store()
  {

/*    DB::beginTransaction();
    try {*/
      //dd(Input::all());
      $input = Input::all();
      $rules = array(
        'fecha'       => 'required|Date'
      );

      $messages = [
        'required'    => 'Informacion requerida!',
        'date'      => 'Fecha invalida!'
      ];              
      
      $validation = \Validator::make($input, $rules, $messages);  
      
      if ($validation->passes())
      {
        
        $year= Carbon::parse(Input::get('fecha'))->year;
        $month= Carbon::parse(Input::get('fecha'))->month;
        $pdo= Sity::getMonthName($month).'-'.$year;
        
        // verifica si ya el periodo existe
        $existePeriodo= Pcontable::where('fecha', Input::get('fecha'))->first();
        //dd($periodo);

        if ($existePeriodo) {
          Session::flash('warning', 'Periodo '.$existePeriodo->periodo.' ya existe no pueden haber duplicados.');
          return Redirect::back();        
        }
        
        // crea un nuevo periodo contable
        Sity::periodo(Input::get('fecha'));
        
        $year= Carbon::parse(Input::get('fecha'))->year;
        $month= Carbon::parse(Input::get('fecha'))->month;

        // crea facturacion para el nuevo periodo contable
        // facturacion para las secciones que generan las ordenes de cobro los dias 1
        Sity::facturar(Carbon::createFromDate($year, $month, 1));
        // facturacion para las secciones que generan las ordenes de cobro los dias 16
        Sity::facturar(Carbon::createFromDate($year, $month, 16));
        
        // Registra en bitacoras
        $detalle =  'Se crea el primer periodo contable del sistema '.$pdo;
      
        Sity::RegistrarEnBitacora(1, 'pcontables', 1, $detalle);
        //DB::commit();        
        Session::flash('success', 'Se crea el primer periodo contable del sistema '.$pdo. ' con éxito.');

        return Redirect::route('pcontables.index');
      }
      return Redirect::back()->withInput()->withErrors($validation);
    
/*    } catch (\Exception $e) {
        DB::rollback();
        Session::flash('warning', ' Ocurrio un error en el modulo PcontablesController.store, la transaccion ha sido cancelada!');

        return Redirect::back()->withInput()->withErrors($validation);
    }*/
  } 

} // end of class