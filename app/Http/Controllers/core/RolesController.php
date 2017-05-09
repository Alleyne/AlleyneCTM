<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo, Session, DB;
use App\library\Sity;

use App\User;
use App\Role;
use App\Role_user;
use App\Permission_role;
use App\Permission;
use App\Bitacora;

class RolesController extends Controller
{
	public function __construct() {
		$this->middleware('hasAccess');    
	}
	
	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function index() {
		//Encuentra todos los roles registrados 
		$datos = Role::all();
		//dd($datos->toArray());
				
		//return response()->json($datos->toArray());
		return view('core.roles.index')->with('datos', $datos);
	}   

	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function permisPorRole($role_id) {
		//Encuentra todos los permisos registrados 
		$datos = Role::find($role_id)->permissions;
		$datos_1 = $datos->pluck('name', 'id')->all();
		//dd($datos_1);

		//Obtiene todos los permisos registrados en la base de datos
		$datos_2= Permission::orderBy('name')->get();
		$datos_2= $datos_2->pluck('name', 'id')->all();      
		//dd($datos_1, $datos_2);
				
		// Subtrae de la lista total de permisos registrados todos aquellos
		// permisos que ya están vinculados a un role
		// para evitar vincular permisos previamente vinculados.
		$permisos = array_diff($datos_2, $datos_1);		
		//dd($permisos);  
		
		return view('core.roles.permisPorRole')
				->with('datos', $datos)
				->with('role_id', $role_id)
				->with('permisos', $permisos);
	}

	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function usuariosPorRole($role_id) {
		//Encuentra todos los usuarios vinculados al role en estudio 
		$datos = Role::find($role_id)->users;
		$datos_1 = $datos->pluck('nombre_completo', 'id')->all();
		//dd($datos_1);

		//Obtiene todos los permisos registrados en la base de datos
		$datos_2= User::where('activated', 1)->orderBy('nombre_completo')->get();
		$datos_2= $datos_2->pluck('nombre_completo', 'id')->all();      
		//dd($datos_1, $datos_2);
				
		// Subtrae de la lista total de permisos registrados todos aquellos
		// permisos que ya están vinculados a un role
		// para evitar vincular permisos previamente vinculados.
		$usuarios = array_diff($datos_2, $datos_1);		
		//dd($usuarios);  
		
		return view('core.roles.usuariosPorRole')
				->with('datos', $datos)
				->with('role_id', $role_id)
				->with('usuarios', $usuarios);
	}

	/*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ************************************************************************************/	
	public function store() {
		
		DB::beginTransaction();
		try {
			//dd(Input::all());
			$input = Input::all();
			$rules = array(
				'id'    	=> 'required'
			);

			$messages = [
				'required' => 'El campo :attribute es requerido!',
				'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				
				$role=Role::find(Input::get('role_id'));
				$role->permissions()->attach(Input::get('id'));		
				
				$permisname = Permission::find(Input::get('id'));
				
			  // Registra en bitacoras
				$detalle = 'Vincula permiso "'.$permisname->name.'" a role '.$role->name;
			  $tabla = 'permission_role';
			  $registro = Permission_role::all()->last()->id;
			  $accion = 'Vincula permiso a role';
			  Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

				DB::commit();				
				
				//return response()->json(["mensaje" => 'La Junta Directiva ' .$dato->nombre. ' ha sido creada con éxito.']); //api
				Session::flash('success', 'El permiso "' .$permisname->name.'" ha sido vinculado al role '.$role->name);
				return back();
			}
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en RolesController.store, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}

	/*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ************************************************************************************/	
	public function roleStoreUsuario() {
		DB::beginTransaction();
		try {

			//dd(Input::all());
			$input = Input::all();
			$rules = array(
				'id'    	=> 'required'
			);

			$messages = [
				'required' => 'El campo :attribute es requerido!',
				'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				
				$role = Role::find(Input::get('role_id'));
				$role->users()->attach(Input::get('id'));		
				
				$username = User::find(Input::get('id'));

			  // Registra en bitacoras
				$detalle =	'Vincula usuario "'.$username->nombre_completo.'" a role '.$role->name;
			  $tabla = 'role_user';
			  $registro = Role_user::all()->last()->id;
			  $accion = 'Vincula usuario a role';
			  
			  Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);
				DB::commit();

				Session::flash('success', 'El usuario "'.$username->nombre_completo.'" ha sido vinculado(a) al role '.$role->name);
				return back();
			}
			return back()->withInput()->withErrors($validation);
	
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en RolesController.roleStoreUsuario, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}

	/*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ************************************************************************************/	
	public function desvincularpermis($role_id, $permission_id) {
		DB::beginTransaction();
		try {		
		
			// encuentra el id del registro a desvincular
			$registo_id = Permission_role::where('role_id', $role_id)->where('permission_id', $permission_id)->first()->id;

			$role = Role::find($role_id);
			$role->permissions()->detach($permission_id);		

			$permisname = Permission::find($permission_id);

		  // Registra en bitacoras
			$detalle =	'Desvincula permiso "'.$permisname->name.'" del role '. $role->name;
		  $tabla = 'permission_role';
		  $registro = $registo_id;
		  $accion = 'Desvincula permiso de role';
		  
		  Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

			DB::commit();

			Session::flash('success', 'El permiso "' .$permisname->name.'" ha sido desvinculado del role '. $role->name);
			return redirect()->route('permisPorRole', $role_id);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en RolesController.desvincularpermis, la transaccion ha sido cancelada!');
			return back();
		}

	}


	/*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ************************************************************************************/	
	public function desvincularUsuario($role_id, $user_id) {
		DB::beginTransaction();
		try {
			
			if ($user_id == Auth::user()->id) {
				Session::flash('warning', 'Usted mismo(a) no se puede desvincular del presente role, solamente el Super administrador o la Junta Directiva lo pueden hacer');
				return back();
			}
			
			// encuentra el id del registro a desvincular
			$registo_id = Role_user::where('role_id', $role_id)->where('user_id', $user_id)->first()->id;

			$role = Role::find($role_id);
			$role->users()->detach($user_id);		

			$username = User::find($user_id);

		  // Registra en bitacoras
			$detalle =	'Desvincula usuario "'.$username->nombre_completo.'" del role '.$role->name;
		  $tabla = 'role_user';
		  $registro = $registo_id;
		  $accion = 'Desvincula usuario de role';
		  
		  Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

			DB::commit();

			Session::flash('success', 'Desvincula usuario "'.$username->nombre_completo.'" del role '.$role->name);
			return redirect()->route('usuariosPorRole', $role_id);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en RolesController.desvincularUsuario, la transaccion ha sido cancelada!');
			return back();
		}
	}
}