<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use App\library\Sity;
use Grupo;
use Validator;
use Image;

use App\Prop;
use App\Un;
use App\Ctdasm;
use App\User;
use App\Bitacora;

class PropsController extends Controller {

    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }

    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function indexprops($un_id, $seccione_id)
	{
	    //Obtiene todos los propietarios de una determinada unidad.
	    $datos = Prop::where('un_id', $un_id)
	            	 ->with('user')
	            	 ->get();
	    //dd($datos->toArray());

  		return view('core.props.indexProps')->with('datos', $datos)
  											   ->with('seccione_id', $seccione_id)
  											   ->with('un_id', $un_id); 
	}	

   /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function createprop($un_id, $seccione_id)
	{
 	    
	    //Obtiene todos los propietarios registrados en la base de datos.
 	    $datos_1= User::orderBy('email')->pluck('email', 'id')->all();
	    //dd($datos_1);
	    
	    //Obtiene todos los propietarios vinculados a una determinada unidad.
	    $datos_2 = Prop::where('un_id', $un_id)
	    					  ->join('users', 'users.id', '=', 'props.user_id')
	            			  ->orderBy('users.email')
	            			  ->get(array('users.email', 'users.id'));
		
		//dd($datos_2->toArray());   
        
        // Convierte a formato array
        $datos_2 = $datos_2->pluck('email', 'id')->all();
        //dd($datos_2);
        
        // Subtrae de la lista total de los usuarios registrados todos aquellos
        // usuarios que ya están vinculados a la unidad
        // para evitar vincular usuarios previamente vinculados.
		$datos = array_diff($datos_1, $datos_2);		
		//dd($datos);  
        
        return view('core.props.createProp')
        			->with('datos', $datos)
        			->with('un_id', $un_id)
        			->with('seccione_id', $seccione_id);
	}     
    
    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();

        $rules = array(
	        'user_id'			=> 'required'
	    );

        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
        
        $validation = \Validator::make($input, $rules, $messages);      	
    	//dd($input, $rules, $messages);

		if ($validation->passes())
		{
		
			// obtiene los datos del usuario
			$user = User::find(Input::get('user_id'));
			//dd($user->toArray());

			// obtiene los datos de la unidad
			$un = Un::find(Input::get('un_id'));
			//dd($un->toArray());

			$dato_2 = new Prop;
			$dato_2->un_id       	 = Input::get('un_id');			
			$dato_2->user_id  		 = Input::get('user_id');
			$dato_2->encargado	 	 = Input::has('encargado');			
			$dato_2->save();	
			
			if (Input::has('encargado')){
				$encargado = 'encargado';
			}
			
			else {
				$encargado = 'no encargado';
			}			
			
			// crea detalle para bitacora
			$detalle =	$user->first_name . " " . $user->last_name .
						', es vinculado(a) como propietario(a) '. $encargado. '(a) de la Unidad ' . $un->codigo;

			// Registra en bitacoras			
			Sity::RegistrarEnBitacora(6, 'props', $dato_2->id, $detalle);
			Session::flash('success', $detalle);
			return redirect()->route('indexprops', array(Input::get('un_id'), Input::get('seccione_id'), Input::get('goback')));
		}
        return back()->withInput()->withErrors($validation);
	}

    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function desvincularProp($user_id, $un_id)
	{
		
 	    // desvincula al usuario como propietario
	    $dato = Prop::where('user_id', $user_id)
	            	 ->where('un_id', $un_id)
	            	 ->with('user')
	            	 ->first();
		//dd($dato->toArray());
		$dato->delete();
		
		if ($dato->encargado == '1'){
			$encargado = 'encargado';
		}
		
		else {
			$encargado = 'no encargado';
		}

		// obtiene los datos de la unidad
		$un = Un::find($dato->un_id);
		//dd($un->toArray());

		// Registra en bitacoras
		$detalle =	$dato->user->nombre_completo .
					', es desvinculado(a) como propietario(a) ' . $encargado . '(a) de la Unidad ' . $un->codigo;
		
		Session::flash('success', $dato->user->nombre_completo .
					', ha sido desvinculado(a) como propietario(a) ' . $encargado . '(a) del la Unidad ' . $un->codigo);

		
		Sity::RegistrarEnBitacora(7, 'props', $dato->id, $detalle);
		return redirect()->route('indexprops', array($un->id, $un->seccione_id));
	}
} 