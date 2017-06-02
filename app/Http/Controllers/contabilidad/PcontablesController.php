<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB, Validator, Cache, URL;
use Carbon\Carbon;

use App\library\Sity;
use App\library\Npdo;
use App\library\Fact;
use App\library\Ppago;
use App\Pcontable;
use App\Bitacora;
use App\Un;

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
		
		Cache::forever('goto_pcontables_index', URL::full());
		return view('contabilidad.pcontables.index')->with('datos', $datos);     	
	}	

	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/  
	public function indexPeriodosfrontend()
	{
		//Obtiene todos los Periodos contables.
		$datos = Pcontable::All();
		//dd($datos->toArray());

		return view('contabilidad.pcontables.indexPeriodosfrontend')->with('datos', $datos);      
	} 

	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/  
	public function store()
	{

		DB::beginTransaction();
		try {
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
				
				$fecha = Carbon::parse(Input::get('fecha'))->startOfMonth(); // lleva la fecha al primer dia del mes
				$year = $fecha->year;
				$month = $fecha->month;
				$pdo = Sity::getMonthName($month).'-'.$year;
				
				// verifica si ya el periodo existe
				$existePeriodo= Pcontable::whereDate('fecha', $fecha)->first();
				//dd($periodo);

				if ($existePeriodo) {
					Session::flash('warning', 'Periodo '.$existePeriodo->periodo.' ya existe no pueden haber duplicados.');
					return back();        
				}
				
				// 1. crear un nuevo periodo contable
				// 2. inicializa en el libro mayor todas las cuentas temporales activas presentes en el catalogo de cuentas, no registra en el diario principal.
				// 3. calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento regular para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes.
				// 4. calcula y contabiliza en libros los ingresos esperados en cuotas de mantenimiento extraordinarias para todas las secciones cuya ocobro se genera los dias primero o dieciseis de cada mes.
				Npdo::periodo($fecha);
				
				// crea facturacion para el nuevo periodo contable
				// facturacion para las secciones que generan las ordenes de cobro los dias 1
				Fact::facturar(Carbon::createFromDate($year, $month, 1));

				// facturacion para las secciones que generan las ordenes de cobro los dias 16
				Fact::facturar(Carbon::createFromDate($year, $month, 16));
							
				DB::commit(); 				

				Session::flash('success', 'Se crea el primer periodo contable del sistema '.$pdo. ' con éxito.');
				return redirect()->route('pcontables.index');
			}
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo PcontablesController.store, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	} 

} // end of class