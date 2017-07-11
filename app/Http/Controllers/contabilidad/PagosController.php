<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Auth, Session, DB, Grupo, Validator;
use App\library\Sity;
use App\library\Npago;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Bitacora;
use App\Hash;
use App\Pago;
use App\Un;
use App\Ctdasm;
use App\Detallepago;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Pcontable;
use App\Banco;
use App\Ctmayore;
use App\Secapto;
use App\Trantipo;
use App\Diariocaja;

class PagosController extends Controller {
    
  public function __construct()
  {
    $this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega todos los pagos que pertenecen a una determinada Unidad en el backend.
   ************************************************************************************/	
	public function indexPagos($un_id)
	{

		$datos = Pago::where('un_id', $un_id)->get();
		//dd($datos->toArray());
		
		return view('contabilidad.pagos.index')
					->with('un_id', $un_id)
					->with('datos', $datos);
	}	

  /*************************************************************************************
   * Despliega todos los pagos que pertenecen a una determinada Unidad en el frontend.
   ************************************************************************************/	
	public function indexPagosfrontend($un_id, $codigo)
	{

		$datos= Pago::where('un_id', $un_id)->get();
		$datos= $datos->sortByDesc('id');
		//dd($datos->toArray());
		
		return view('contabilidad.pagos.indexPagosfrontend')
					->with('un_id', $un_id)
					->with('codigo', $codigo)
					->with('datos', $datos);
	}

  /*************************************************************************************
	 * Despliega formulario para crear un nuevo registro
	 ************************************************************************************/	
	public function createPago($un_id, $key)
	{
    
    // obtiene todas las instituciones bancarias actualmente registrada
    $bancos = Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
		//dd($bancos);	    
    
    if ($key == 1) {
	    return view('contabilidad.pagos.createPagoTipo1')        			
						->with('bancos', $bancos)
						->with('key', $key)
						->with('un_id', $un_id);
    
    } elseif ($key == 4) {
	    return view('contabilidad.pagos.createPagoTipo4')        			
						->with('bancos', $bancos)
						->with('key', $key)
						->with('un_id', $un_id);
    
    } elseif ($key == 5 || $key == 7) {
	    return view('contabilidad.pagos.createPagoTipo57')        			
						->with('bancos', $bancos)
						->with('key', $key)
						->with('un_id', $un_id);
    
    } elseif ($key == 6) {
	    return view('contabilidad.pagos.createPagoTipo6')        			
						->with('bancos', $bancos)
						->with('key', $key)
						->with('un_id', $un_id);
    }
    	
	} 

  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function store()
	{
        
		DB::beginTransaction();
		try {
      //dd(Input::all());
      $input = Input::all();

      $f_final= Carbon::today()->addDay(1);

      // 
      if (Input::get('key')== '1') {
	      $rules = array(
          'transno'   	=> 'Required|Numeric|digits_between:1,10|min:1',
          'monto'   	 	=> 'required|Numeric|min:0.01',
          'f_pago'   	 	=> 'required|Date|Before:'.$f_final,
          'descripcion'	=> 'required',
          'un_id'		 		=> 'required'            
	      );  
      
      } elseif (Input::get('key')== '2' || Input::get('key')== '3' || Input::get('key')== '4') {
	      $rules = array(
          'banco_id'   	=> 'required|not_in:0',
          'monto'   	 	=> 'required|Numeric|min:0.01',
          'f_pago'   	 	=> 'required|Date|Before:'.$f_final,
          'descripcion'	=> 'required',
          'un_id'		 		=> 'required'            
	      );  
      
      } elseif (Input::get('key')== '5' || Input::get('key')== '7' || Input::get('key')== '8' || Input::get('key')== '9') {
	      $rules = array(
          'monto'   	 	=> 'required|Numeric|min:0.01',
          'f_pago'   	 	=> 'required|Date|Before:'.$f_final,
          'descripcion'	=> 'required',
          'un_id'		 		=> 'required'            
	      );      
      
      } elseif (Input::get('key')== '6') {
	      $rules = array(
          'banco_id'   	=> 'required|not_in:0',
          'monto'   	 	=> 'required|Numeric|min:0.01',
          'f_pago'   	 	=> 'required|Date|Before:'.$f_final,
          'descripcion'	=> 'required',
          'un_id'		 		=> 'required'            
	      );   
      }

      //dd($rules, $messages);
      
      $validation = \Validator::make($input, $rules);      	

			if ($validation->passes())
			{
				// verifica si existe registro para informe de diario de caja para el dia de hoy,
				// si no existe entonces lo crea.
				if (Input::get('key') != '2' && Input::get('key') != '3' && Input::get('key') != '4') {
					$diariocaja= Diariocaja::where('fecha', Input::get('f_pago'))->first();

			    if (!$diariocaja) {
			    	$dato = new Diariocaja; 
				    $dato->fecha= Input::get('f_pago'); 		    
				    $dato->save();
			    }
				}
		    
		    // calcula el periodo al que corresponde la fecha de pago
		    $f_pago= Carbon::parse(Input::get('f_pago'));
		    $year= $f_pago->year;
		    $month= $f_pago->month;
		    $pdo= Sity::getMonthName($month).'-'.$year;    
			    
			  // encuentra el periodo mas antiguo abierto
				$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
		    //dd($pdo, $periodo->periodo);
		    
		    // solamente se permite registrar pagos que correspondan al periodo mas antiguo abierto
		    if ($pdo != $periodo->periodo) {
          Session::flash('danger', '<< ERROR >> Solamente se permite registrar pagos que correspondan al periodo de '.$periodo->periodo);
      		return back()->withInput()->withErrors($validation);
		    }

				// antes de iniciar el proceso de pago, ejecuta el proceso de penalizacion
				Npago::penalizarTipo2(Input::get('f_pago'), Input::get('un_id'), $periodo->id);

				// Almacena el monto de la transaccion
				$montoRecibido= round(floatval(Input::get('monto')),2);

				// Procesa el pago recibido	si el tipo de transaccion es cheque
				if (Input::get('key') == 1) {
					// Solamente registra el pago recibido no lo procesa
					$dato = new Pago;
					$dato->banco_id    = Input::get('banco_id');
					$dato->trantipo_id = Input::get('key');
				  $dato->trans_no    = Input::get('transno'); 
					$dato->monto       = $montoRecibido;
					$dato->f_pago      = Input::get('f_pago');
					$dato->descripcion = Input::get('descripcion');
					$dato->concepto		 = 'pago por servicios de mantenimiento';
				  $dato->fecha 	   	 = Carbon::today(); 		    
					$dato->entransito  = 1;
					$dato->un_id       = Input::get('un_id');
			    $dato->user_id 	   = Auth::user()->id; 		    
			    $dato->save();
					
					// Registra en bitacoras
					Sity::RegistrarEnBitacora($dato, Input::get(), 'Pago', 'Registra pago de propietario');

					DB::commit();	
		      Session::flash('success', 'El pago ' .$dato->id. ' ha sido creado con éxito.');			
			
				} else {
					
					// Registra el pago recibido
					$dato = new Pago;
					$dato->banco_id    = Input::get('banco_id');
					$dato->trantipo_id = Input::get('key');
				  $dato->trans_no    = Input::get('transno'); 
					$dato->monto       = $montoRecibido;
					$dato->f_pago      = Input::get('f_pago');
					$dato->descripcion = Input::get('descripcion');
					$dato->concepto		 = 'pago por servicios de mantenimiento';
				  $dato->fecha 	   	 = Carbon::today(); 		    
					$dato->entransito  = 0;
					$dato->un_id       = Input::get('un_id');
			    $dato->user_id 	   = Auth::user()->id; 		    
			    $dato->save();

					$tipoPago = Input::get('key');
					//dd($tipoPago);
					
					// proceso de contabilizar el pago recibido
					Npago::iniciaPago(Input::get('un_id'), $montoRecibido, $dato->id, Input::get('f_pago'), $periodo->id, $periodo->periodo, $tipoPago);

					// Registra en bitacoras
  				Sity::RegistrarEnBitacora($dato, Input::get(), 'Pago', 'Registra pago de propietario');

					DB::commit();		            
		      Session::flash('success', 'El pago ' .$dato->id. ' ha sido registrado con éxito.');
				}
				return redirect()->route('indexPagos',  Input::get('un_id'));
			}
	    return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', 'Ocurrio un error en el modulo PagosController.store, la transaccion ha sido cancelada! '.$e->getMessage());
			return back()->withInput()->withErrors($validation);
		}
	}

  /*************************************************************************************
   * Despliega todos los pagos que pertenecen a una determinada Unidad.
   ************************************************************************************/	
	public function showRecibo($pago_id)
	{
 		// encuentra las generales de pago
		$pago = Pago::find($pago_id);
    //dd($pago);

 		// encuentra todos los detalles del pago, excluye el renglon de notas
    $detalles= $pago->detallepagos()->where('no','!=',0)->get();

		// calcula el total	pagado
		$total= $pago->detallepagos()->where('no','!=',0)->sum('monto');		

 		// determina si existe alguna nota en detalles de pagos
    $nota= $pago->detallepagos()->where('no',0)->first();
		if ($nota) {
			$nota= $nota->detalle;
		} else {
			$nota= "";
		}
		//dd($nota);    
    
 		// determina si la unidad tiene algun propietario encargado
 		$prop = $pago->un->props()->where('encargado', '1')->first();
		//dd($prop->user->nombre_completo);
 		
 		if (empty($prop)) {
			Session::flash('warning', 'Esta Unidad no tiene propietario encargado asignado. Favor asignar un propietario como responsable.');
			return back();
	  } 

		return view('contabilidad.pagos.showRecibo')
					->with('pago', $pago)
					->with('prop', $prop)
					->with('total', $total)
					->with('detalles', $detalles)
					->with('nota', $nota);
	}	

  /*************************************************************************************
   * Anula pago
   ************************************************************************************/	
	public function procesaAnulacionPago($pago_id, $un_id)
	{
    
		DB::beginTransaction();
		try {
	    // Encuentra el o los periodos contables que fueron involucrados en el pago.
	    // No permite anular el pago si el mismo involucra por lo menos a un periodo ya cerrado.
	    $periodos=Ctmayore::where('pago_id', $pago_id)->select('pcontable_id')->get();
			$periodos=$periodos->unique('pcontable_id');
			//dd($periodos->toArray());
			
			foreach ($periodos as $periodo) {
		    // verifica si alguno de los datos pertenece a un periodo ya cerrado
		    $pdo= Pcontable::where('id', $periodo->pcontable_id)
		    								->where('cerrado', 1)
		                    ->first();
		    
		    // solamente se pueden anular pagos que pertenezcan al periodo mas antiguo que no este cerrado
		    if ($pdo) {
		    	Session::flash('warning', '<< ATENCION >> No se puede anular este pago ya que involucra a uno o mas periodo contables ya cerrados!');
	    		return redirect()->route('indexPagos',  $un_id);	
 		    }	
			}
			
	 		//Encuentra todos lo registros de pago que son posteriores al pago en estudio
			$datos = Pago::where('un_id', $un_id)
	                     ->where('id','>',$pago_id)
	                     ->where('anulado',0)
	                     ->first();
	    //dd($datos);		    
	    
	    if ($datos) {
	    	Session::flash('warning', '<< ATENCION >> No se puede anular el Pago No. '.$pago_id.' ya que exite uno o mas pagos posteriores al mismo. Para poder anular el presente pagos, debera anular todos los pagos posteriores en orden cronologico!');
    		return redirect()->route('indexPagos', $un_id);	
			}
			
			// procede a anular el pago
	    Npago::anulaPago($pago_id);
			
			DB::commit();	    	
    	
    	Session::flash('warning', 'Pago '.$pago_id. ' ha sido anulado.');	   
	    return redirect()->route('indexPagos', $un_id);
		
		} catch (\Exception $e) {
	    DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo PagosController.procesaAnulacionPago, la transaccion ha sido cancelada! '.$e->getMessage());

      return back()->withInput()->withErrors($validation);
		}    
	}	

  /*************************************************************************************
   * Anula pago
   ************************************************************************************/	
	public function eliminaPagoCheque($pago_id)
	{
	    
		DB::beginTransaction();
		try {
			$pago=Pago::find($pago_id);
			$pago->delete(); 

			$detalle="Elimmina pago de cheque no contabilizado de la base de datos";
			Sity::RegistrarEnBitacora(3, 'pagos', $pago->id, $detalle);
			Session::flash('warning', 'Pago '. $pago->trans_no . ' ha sido eliminado permanentemente de la base de datos!');
			DB::commit();		    
			return redirect()->route('indexPagos',  $pago->un_id);
	
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo PagosController.eliminaPagoCheque, la transaccion ha sido cancelada! '.$e->getMessage());

			return back()->withInput()->withErrors($validation);
		}
	}	

    /*************************************************************************************
	 * Procesa pago
	 ************************************************************************************/	
	public function procesaChequeRecibido($pago_id)
	{
		
		DB::beginTransaction();
		try {
			// Procesa el pago recibido			
			$dato = Pago::where('pagos.id', $pago_id)
	                    ->select('un_id','monto','id','f_pago')
	                    ->first();
			//dd($dato->toArray());
			
			// encuentra la fecha del periodo contable mas antiguo abierto
			$periodo= Pcontable::where('cerrado', 0)->orderBy('id', 'asc')->first();
			//dd($periodo->fecha);  

			// proceso de contabilizar el pago recibido
			Npago::iniciaPago($dato->un_id, $dato->monto, $dato->id, $dato->f_pago, $periodo->id, $periodo->periodo, 1);

			// Registra el pago como tramitado
			$dato1 = Pago::find($pago_id);
			$dato1->entransito = 0;
		  $dato1->save(); 

				// Registra en bitacoras
			$detalle = 'Pago No '.$dato1->id.', con cheque No '.$dato1->trans_no.' se ha registrado con exito.';
			$tabla = 'pagos';
			$registro = $dato1->id;
			$accion = 'Contabiliza pago por cheque';

			Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

			DB::commit();    
			Session::flash('success', 'El pago No.' .$dato->id. ' ha sido registrado con éxito.');
			return redirect()->route('indexPagos',  $dato->un_id);
	
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo PagosController.procesaChequeRecibido, la transaccion ha sido cancelada! '.$e->getMessage());
			return redirect()->route('indexPagos',  $dato->un_id);
		}
	} 
}