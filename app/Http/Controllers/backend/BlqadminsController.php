<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Redirect, Session;
use Grupo;
use Validator;
use Image;

use App\Bloque;
use App\Blqadmin;
use App\Org;
use App\User;
use App\Bitacora;

class BlqadminsController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function indexblqadmin($bloque_id)
	{
 	    //Obtiene todos los propietarios de un determinado bloque.
	    $datos = Blqadmin::where('bloque_id', $bloque_id)
	            			->with('Bloque')
	            			->with('User')
	            			->with('Org')		
	            			->get();
	    //dd($datos->toArray());
	    
	    //Obtiene el id de la junta directiva
	    $jd_id = Bloque::find($bloque_id)->jd_id;
        
        //------datos para el Modal box

	    //Obtiene todos los propietarios registrados en la base de datos.
 	    $datos_1= User::orderBy('email')->pluck('email', 'id')->all();
	    //dd($datos_1);
	    
	    //Obtiene todos los blqadmins vinculados a un determinado bloque.
	    $datos_2  = Blqadmin::where('bloque_id', $bloque_id)
      						->with('user')
      						->get();
		//dd($datos_2->toArray());   
        
        // Convierte a formato array
        $datos_2 = $datos_2->pluck('user.email', 'user.id')->all();
        //dd($datos_2);
        
        // Subtrae de la lista total de los usuarios registrados todos aquellos
        // usuarios que ya están vinculados a la unidad
        // para evitar vincular usuarios previamente vinculados.
		$usuarios = array_diff($datos_1, $datos_2);		
		//dd($usuarios);  
        
	    //Obtiene todos las organizaciones actualmente registrada en la base de datos.
 	    $orgs= Org::orderBy('nombre')->get();
	    $orgs= $orgs->pluck('nombre', 'id')->all();              
		//dd($orgs);        
        //------datos para el Modal box        
   		
  		return view('backend.blqadmins.indexblqadmin')
  					->with('datos', $datos)
  					->with('usuarios', $usuarios)     	                
  					->with('orgs', $orgs)
  					->with('bloque_id', $bloque_id)     
  					->with('jd_id', $jd_id);   
	}	

    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
    
		if (Input::get('cargo')=='0') {
	        $rules = array(
		        'user_id'			=> 'required'
		        );
		}
		elseif (Input::get('cargo')=='1') {
	        $rules = array(
		        'user_id'			=> 'required',
		        'org_id'			=> 'required'
		        );
		}

        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
        
        $validation = \Validator::make($input, $rules, $messages);      	
    	//dd($input, $rules, $messages);

		if ($validation->passes())
		{
			// agrega el nuevo administrador de bloque
			$dato = new Blqadmin;
			$dato->bloque_id         = Input::get('bloque_id');			
			$dato->user_id  		 = Input::get('user_id');
			if (Input::get('cargo')=='1') {
				$dato->org_id = Input::get('org_id'); 
			}
			$dato->cargo             = Input::get('cargo');					
			$dato->encargado		 = Input::has('encargado');			
			$dato->save();	
			
			// prepara los datos para la bitacora
			$cargo = (Input::get('cargo')=='0' ? 'Persona Natural' : 'Persona Jurídica');
			$encargado = (Input::has('encargado') ? 'encargado' : 'no encargado');
			$user = User::find(Input::get('user_id'));
			$bloque = Bloque::find(Input::get('bloque_id'));
			$org = Org::find(Input::get('org_id'));
			
			if (Input::has('org_id')) {			
				$detalle =	$user->nombre_completo .
							', bajo el cargo de '. $cargo .
							', en ' . $org->nombre .
							', es vinculado(a) como administrador(a) '. $encargado. '(a) del Bloque "' . $bloque->nombre . '"';
			}
			else {
				$detalle =	$user->nombre_completo .
							', bajo el cargo de '. $cargo .
							', es vinculado(a) como administrador(a) '. $encargado. '(a) del Bloque "' . $bloque->nombre . '"';
			}

			// Registra en bitacoras			
			Sity::RegistrarEnBitacora(8, 'blqadmins', $dato->id, $detalle);
			Session::flash('success', $detalle);
			return Redirect::route('indexblqadmin', array(Input::get('bloque_id')));
		}
		Session::flash('warning', 'Se encontraron errores en su formulario, inténtelo nuevamente!');
        return Redirect::back()->withInput()->withErrors($validation);
	}
    
     /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function desvincularblqdmin($id)
	{
	    // desvincula al usuario como administrador de bloque
		$dato = Blqadmin::with('org')
						->with('user')
						->with('bloque')
						->find($id);
		$dato->delete();
 		
		// prepara los datos para la bitacora
		$cargo = ($dato->cargo =='0' ? 'Persona Natural' : 'Persona Jurídica');
		$encargado = ($dato->encargado =='0' ? 'no encargado' : 'encargado');

		if ($dato->org_id==null) {			
			$detalle =	$dato->user->nombre_completo .
						', bajo el cargo de '. $cargo .
						', es desvinculado vinculado(a) como administrador(a) '. $encargado. '(a) del Bloque "' . $dato->bloque->nombre . '"';			
		}
		else {
			$detalle =	$dato->user->nombre_completo .
						', bajo el cargo de '. $cargo .
						', en ' . $dato->org->nombre .
						', es desvinculado(a) como administrador(a) '. $encargado. '(a) del Bloque "' . $dato->bloque->nombre . '"';
		}

		// Registra en bitacoras			
		Sity::RegistrarEnBitacora(9, 'blqadmins', $dato->id, $detalle);
		Session::flash('success', $detalle);
		return Redirect::route('indexblqadmin', $dato->bloque->id);	
	}
} 