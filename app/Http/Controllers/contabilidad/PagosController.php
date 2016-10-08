<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Auth;
use App\library\Sity;
use Redirect, Session;
use Grupo;
use Validator;
use Carbon\Carbon;
use Debugbar;
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

class PagosController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega todos los pagos que pertenecen a una determinada Unidad.
     ************************************************************************************/	
	public function indexPagos($un_id)
	{
 		//Encuentra todos lo registros de pago 
		$datos = Pago::where('un_id', $un_id)
					 ->join('bancos', 'bancos.id', '=', 'pagos.banco_id')
                     ->select('pagos.entransito','pagos.id','bancos.nombre','pagos.f_pago','pagos.monto','pagos.un_id','pagos.anulado','pagos.trans_tipo','pagos.trans_no')
                     ->get();
	    
	    Carbon::setLocale('es');	    
	    $i=0;

	    foreach ($datos as $dato) {
	      $dato->f_pago=Carbon::createFromFormat('Y-m-d', $dato->f_pago)->format('M j\\, Y');
	      if ($dato->trans_tipo==1) {
	      	$dato->trans_tipo='Cheque';
	      } elseif ($dato->trans_tipo==2) {
	      	$dato->trans_tipo='Transferencia';
	      }
	      $i++;
	    }    

		//dd($datos->toArray());
		return \View::make('contabilidad.pagos.index')
					->with('datos', $datos)
					->with('un_id', $un_id);   	
	}	

    /*************************************************************************************
	 * Despliega formulario para crear un nuevo registro
	 ************************************************************************************/	
	public function createPago($un_id)
	{
	    //dd('aqui');
	    
	    //Obtiene todas las instituciones bancarias actualmente registrada en la base de datos.
	    $bancos= Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
		//dd($bancos);	    
	    
	    return \View::make('contabilidad.pagos.createPago')        			
  					->with('bancos', $bancos)
  					->with('un_id', $un_id);    	
	} 

    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();

        $f_final=Carbon::today()->addDay(1);

        $rules = array(
            'banco_id'   => 'required|not_in:0',
            'trans_tipo' => 'required|not_in:0',
            'trans_no'   => 'Required|Numeric|digits_between:1,10|min:1',
            'monto'   	 => 'required|Numeric|min:0.01',
            'f_pago'   	 => 'required|Date|Before:' . $f_final,
            'descripcion'=> 'required',
            'un_id'		 => 'required'            
        );
    
      	$messages = [
            'required'		=> 'Informacion requerida!',
            'before'		=> 'La fecha del pago debe ser anterior o igual a fecha del dia de hoy!',
        	'digits_between'=> 'El numero de la transaccion debe tener de uno a diez digitos!',
        	'numeric'		=> 'Solo se admiten valores numericos!',
        	'date'			=> 'Fecha invalida!',
        	'min'			=> 'El monto del pago debe ser mayor que cero!'
        ];         
        //dd($rules, $messages);
        
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
		    // calcula el periodo al que corresponde la fecha de pago
		    $year= Carbon::parse(Input::get('f_pago'))->year;
		    $month= Carbon::parse(Input::get('f_pago'))->month;
		    $pdo= Sity::getMonthName($month).'-'.$year;    
		    
		    // encuentra el periodo mas antiguo abierto
			$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
		    //dd($periodo);
		    
		    // solamente se permite registrar pagos que correspondan al periodo mas antiguo abierto
		    if ($pdo != $periodo->periodo) {
	            Session::flash('danger', '<< ERROR >> Solamente se permite registrar pagos que correspondan al periodo de '.$periodo->periodo);
        		return Redirect::back()->withInput()->withErrors($validation);
		    }

			// Almacena el monto de la transaccion
			$montoRecibido= round(floatval(Input::get('monto')),2);

			// Procesa el pago recibido	si el tipo de transaccion es cheque
			if (Input::get('trans_tipo')==1) {
				// Solamente registra el pago recibido no lo procesa
				$dato = new Pago;
				$dato->banco_id    = Input::get('banco_id');
				$dato->trans_tipo  = Input::get('trans_tipo');
			    $dato->trans_no    = Input::get('trans_no'); 
				$dato->monto       = $montoRecibido;
				$dato->f_pago      = Input::get('f_pago');
				$dato->descripcion = Input::get('descripcion');
			    $dato->fecha 	   = Carbon::today(); 		    
				$dato->entransito  = 1;
				$dato->un_id       = Input::get('un_id');
			    $dato->user_id 	   = Auth::user()->id; 		    
			    $dato->save();
				// Registra en bitacoras
				$detalle =	'Registra pago de cuota de mantenimiento No.'.$dato->id.' con monto de B/.'.$montoRecibido.' no contabiliza';  
	            
	            Sity::RegistrarEnBitacora(1, 'pagos', $dato->id, $detalle);
	            Session::flash('success', 'El pago ' .$dato->id. ' ha sido creado con éxito.');			
		
			} elseif (Input::get('trans_tipo')==2) {
				
				// Registra el pago recibido
				$dato = new Pago;
				$dato->banco_id    = Input::get('banco_id');
				$dato->trans_tipo  = Input::get('trans_tipo');
			    $dato->trans_no    = Input::get('trans_no'); 
				$dato->monto       = $montoRecibido;
				$dato->f_pago      = Input::get('f_pago');
				$dato->descripcion = Input::get('descripcion');
			    $dato->fecha 	   = Carbon::today(); 		    
				$dato->entransito  = 0;
				$dato->un_id       = Input::get('un_id');
			    $dato->user_id 	   = Auth::user()->id; 		    
			    $dato->save();

				// proceso de contabilizar el pago recibido
				Sity::iniciaPago(Input::get('un_id'), $montoRecibido, $dato->id, Input::get('f_pago'), $periodo->id);

				// Registra en bitacoras
				$detalle =	'Crea y procesa Pago de mantenimiento '. $dato->id. ', con el siguiente monto: '.  $dato->monto;  
	            Sity::RegistrarEnBitacora(1, 'pagos', $dato->id, $detalle);
	            Session::flash('success', 'El pago ' .$dato->id. ' ha sido creado y procesado con éxito.');
			}
			return Redirect::route('indexPagos',  Input::get('un_id'));
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}

    /*************************************************************************************
     * Despliega todos los pagos que pertenecen a una determinada Unidad.
     ************************************************************************************/	
	public function showRecibo($pago_id)
	{
 		
 		//Encuentra todos los datos del pago
		$pago = Pago::where('pagos.id', $pago_id)
					->join('bancos', 'bancos.id', '=', 'pagos.banco_id')
                    ->select('pagos.id','pagos.anulado','bancos.nombre','pagos.f_pago','pagos.monto','pagos.un_id')
                    ->first();
    
 		//Encuentra todos los detalles del pago
		$detalles = Detallepago::where('pago_id', $pago_id)
                	    	   ->where('no','!=',0)
                	    	   ->get();

		$total=0;
		// calcula el total	pagado    
	    foreach ($detalles as $detalle) {
			$total  = $total + $detalle->monto;  
	    }       
		$total=number_format($total, 2);
		//dd($detalle->toArray());
		
 		//Determina si hubo aumento o disminucion el cuenta de pagos por anticipados
		$dato = Detallepago::where('pago_id', $pago_id)
                	    	   ->where('no','=',0)
                	    	   ->first();
        if (!empty($dato)) {
        	$nota= $dato->detalle;
        }
		else {
			$nota="";
		}  
	    // Encuentra los datos de la unidad
	    $un = Un::find($pago->un_id);
	    //dd($un->toArray());

	    // Obtiene todos los propietarios de una determinada unidad que sean encardados.  
	    $prop = Prop::where('un_id', $un->id)
	                ->where('encargado', '1')
	                ->with('user')
	                ->first();
	    //dd($prop->toArray()); 

 		if (empty($prop)) {
	      Session::flash('warning', 'Esta Unidad no tiene propietario encargado asignado. Favor asignar un propietario como responsable.');
	        return Redirect::back();
	    } 

	    // Encuentra los datos de la sección a la cual pertenece la unidad
	    $seccion = Seccione::find($un->seccione_id);
	    //dd($seccion->toArray());
	    
	    // Encuentra los datos del Ph al que pertenece la unidad
	    $ph = Ph::find($seccion->ph_id);
	    //dd($ph->toArray()); 

		return \View::make('contabilidad.pagos.showRecibo')
					->with('pago', $pago)
					->with('detalles', $detalles)
					->with('un', $un)
					->with('prop', $prop)
					->with('total', $total)
					->with('nota', $nota)
					->with('ph', $ph);   	
	}	

    /*************************************************************************************
     * Anula pago
     ************************************************************************************/	
	public function procesaAnulacionPago($pago_id, $un_id)
	{
    
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
	    		return Redirect::route('indexPagos',  $un_id);	
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
    		return Redirect::route('indexPagos', $un_id);	
		}
		
		// procede a anular el pago
    	Sity::anulaPago($pago_id);
    	Session::flash('warning', 'Pago '.$pago_id. ' ha sido anulado.');	   
	    return Redirect::route('indexPagos', $un_id);	
	}	

    /*************************************************************************************
     * Anula pago
     ************************************************************************************/	
	public function eliminaPagoCheque($pago_id)
	{
	    $pago=Pago::find($pago_id);
	    $pago->delete(); 
	    
	    $detalle="Elimmina pago de cheque no contabilizado de la base de datos";
	    Sity::RegistrarEnBitacora(3, 'pagos', $pago->id, $detalle);
	    Session::flash('warning', 'Pago '. $pago->trans_no . ' ha sido eliminado permanentemente de la base de datos!');
	    return Redirect::route('indexPagos',  $pago->un_id);	
	}	

    /*************************************************************************************
	 * Procesa pago
	 ************************************************************************************/	
	public function procesaChequeRecibido($pago_id)
	{
		// Procesa el pago recibido			
		$dato= Pago::where('pagos.id', $pago_id)
                    ->select('un_id','monto','id','f_pago')
                    ->first();
		//dd($dato->toArray());
		
		// proceso de contabilizar el pago recibido
		Sity::iniciaPago($dato->un_id, $dato->monto, $dato->id, $dato->f_pago);

		// Registra el pago como tramitado
		$dato1 = Pago::find($pago_id);
		$dato1->entransito = 0;
	    $dato1->save(); 

		// Registra en bitacoras
		$detalle =	'Pago No '. $dato->id. ', se ha registrado con exito.';  
		
        Sity::RegistrarEnBitacora(1, 'pagos', $dato->id, $detalle);
        Session::flash('success', 'El pago No.' .$dato->id. ' ha sido registrado con éxito.');
        return Redirect::route('indexPagos',  $dato->un_id);
	} 
}