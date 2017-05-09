<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo, DB, Session;
use App\library\Sity;

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
		return view('core.permissions.index')->with('datos', $datos);
	}	

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario sólo lectura
	 ************************************************************************************/	
	public function show($id)
	{
		$dato = Permission::find($id);
		if(!empty($dato)) {
			//return response()->json($dato);  //api
			return view('core.permissions.show')->with('dato', $dato);
		
		} else {
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
		return view('core.permissions.create');
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

  			Sity::RegistrarEnBitacora($dato, Input::get(), 'Permission', 'Registra nuevo permiso');
				
				DB::commit();				
				
				//return response()->json(["mensaje" => 'El permiso ' .$dato->name. ' ha sido creado con éxito.']); //api
				Session::flash('success', 'El permiso "' .$dato->name. '" ha sido registrado con éxito.');
				return redirect()->route('permissions.index');
			
			}
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en PermissionsController.store, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}
		
		
	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario para edición
	 ************************************************************************************/	
	public function edit($id)
	{
		return view('core.permissions.edit')->with('dato', Permission::find($id));
	}


	/*************************************************************************************
	 * Actualiza registro
	 ************************************************************************************/
	public function update($id)
	{
		
		DB::beginTransaction();
		try {

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
  			Sity::RegistrarEnBitacora($dato, Input::get(), 'Permission', 'Actualiza permiso');
				$dato->save();			

				DB::commit();

				//return response()->json(["mensaje" => 'El permiso ' .$id. ' ha sido editado con éxito.']); //api
				Session::flash('success', 'El permiso ' .$id. ' ha sido editado con éxito.');
				return redirect()->route('permissions.index');
			}
			
			return back()->withInput()->withErrors($validation);
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en PermissionsController.update, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}
	
	
	/*************************************************************************************
	 * Borra registro de la base de datos
	 ************************************************************************************/	
	public function destroy($id)
	{

		DB::beginTransaction();
		try {
			/*No se permitirá borrar aquellos permisos que esten en uso por algun role.*/
			$dato = Permission::find($id);
			
			// Revisa si hay algún bloque asignado a la junta directiva
			$permissions = Permission::find($id)->roles()->first();
			//dd($permissions->toArray());
			
			if(!empty($permissions)) {
				Session::flash('success', 'El permiso ' .$dato->name. ' no puede ser borrado porque esta asignadoa a uno mas roles.');
				return redirect()->route('permissions.index');	
			}
			
			else {
				$dato->delete();
				Sity::RegistrarEnBitacora($dato, Null, 'Permission', 'Elimina permiso');   
				
				DB::commit();
				
				Session::flash('success', 'El permiso ' .$dato->name. ' ha sido borrado permanentemente de la base de datos.');			
				return redirect()->route('permissions.index');	
			}

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en PermissionsController.destroy, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}

}