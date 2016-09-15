<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo;
use App\library\Sity;
use Redirect, Session;

use App\Permission;
use App\Bitacora;

class PermissionsController extends Controller
{
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function index()
	{
		//Encuentra todos los permisos registrados 
		$datos = Permission::all();
		//dd($datos->toArray());
        
        //return response()->json($datos->toArray());
 		return view('backend.permissions.index')->with('datos', $datos);
	}	

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario sólo lectura
     ************************************************************************************/	
	public function show($id)
	{
	    $dato = Permission::find($id);
	    if(!empty($dato)) {
			//return response()->json($dato);  //api
			return view('backend.permissions.show')->with('dato', $dato);
		}
	    else {
			//return response()->json(["mensaje" => "Permiso no existe!"]); //api			
			Session::flash('danger', 'El permiso No. ' .$id. ' no existe.');
	    	return redirect()->action('PermissionsController@index');
	    }
	}
   
   /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function create()
	{
        return view('backend.permissions.create');
	}     
    
    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $rules = array(
            'name'    	=> 'required|unique:permissions',
            'value'    	=> 'required|unique:permissions'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
			$dato = new Permission;
			$dato->name       = Input::get('name');
			$dato->value       = Input::get('value');
			$dato->description  = Input::get('description');
			$dato->save();	
			
			// Registra en bitacoras
			$detalle =	'name= '.		   $dato->name. ', '.
						'value '.		   $dato->value. ', '.
						'descripcion= '.   $dato->descripcion;

 			Sity::RegistrarEnBitacora(1, 'permissions', $dato->id, $detalle);
			
			//return response()->json(["mensaje" => 'El permiso ' .$dato->name. ' ha sido creado con éxito.']); //api
			Session::flash('success', 'El permiso ' .$dato->name. ' ha sido creado con éxito.');
			return Redirect::route('permissions.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}
    
    
    /*************************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     ************************************************************************************/	
	public function edit($id)
	{
		return view('backend.permissions.edit')->with('dato', Permission::find($id));
	}


    /*************************************************************************************
     * Actualiza registro
     ************************************************************************************/
	public function update($id)
	{
        //dd(Input::get());
        $input = Input::all();
        $rules = array(
            'name'    	=> 'required',
            'value'    	=> 'required'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = Permission::find($id);
			$dato->name       = Input::get('name');
			$dato->value       = Input::get('value');
			$dato->description  = Input::get('description');
			$dato->save();			
			
			// Registra en bitacoras
			$detalle =	'name= '.		   $dato->name. ', '.
						'value '.		   $dato->value. ', '.
						'descripcion= '.   $dato->descripcion;

			Sity::RegistrarEnBitacora(2, 'permissions', $dato->id, $detalle);
			
			//return response()->json(["mensaje" => 'El permiso ' .$id. ' ha sido editado con éxito.']); //api
			Session::flash('success', 'El permiso ' .$id. ' ha sido editado con éxito.');
			return Redirect::route('permissions.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
  	}
  
  
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($id)
	{

		/*No se permitirá borrar aquellos permisos que esten en uso por algun role.*/
		$dato = Permission::find($id);
		
		// Revisa si hay algún bloque asignado a la junta directiva
		$permissions = Permission::find($id)->roles()->first();
		//dd($permissions->toArray());
		
		if(!empty($permissions)) {
			Session::flash('success', 'El permiso ' .$dato->name. ' no puede ser borrado porque esta asignadoa a uno mas roles.');
			return Redirect::route('permissions.index');	
		}
		
		else {
			$dato->delete();

			// Registra en bitacoras
			$detalle =	'Borra el permiso '. $dato->name;
			Sity::RegistrarEnBitacora(3, 'permissions', $dato->id, $detalle);
			
			Session::flash('success', 'El permiso ' .$dato->name. ' ha sido borrado permanentemente de la base de datos.');			
			return Redirect::route('permissions.index');	
		}
	}
}