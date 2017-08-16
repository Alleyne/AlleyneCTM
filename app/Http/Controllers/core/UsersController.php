<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use App\Hash;
use Session, Validator,Image, DB, File;
use Role;

use App\User;
use App\Bitacora;
use App\Blqadmin;
use App\Prop;

class UsersController extends Controller {
		
	public function __construct()
	{
		$this->middleware('hasAccess');    
	}
	
	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function index()
	{
		//Obtiene todos usuarios actualmente registrados en la base de datos.
		$datos = User::orderBy('last_name', 'asc')->get();
		//dd($datos->toArray());
		
		return view('core.users.index')->with('datos', $datos);
	}	

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario sólo lectura
	 ************************************************************************************/	
	public function show($id)
	{
		$dato = User::find($id);
		//dd($dato);
		
		if(!empty($dato)) {
		return view('core.users.show')->with('dato', $dato);
		
		} else {
			Session::flash('danger', 'El Usuario No. ' .$id. ' no existe.');
			return redirect()->route('backend.users.show');	    	
		}
	}
		
	/*************************************************************************************
	 * Despliega formulario para crear un nuevo registro
	 ***********************************************************************************	
	public function create()
	{
				return view('core.users.create');
	}*/  

	/*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ***********************************************************************************	
	public function store()
	{
				//dd(Input::all());
				$input = Input::all();
				$rules = array(
						'username'      		=> 'Required|Min:3|Max:80|Alpha|Unique:users',           
						'email'      			=> 'Required|Between:6,50|Email|Unique:users', 
			'password'  			=> 'Required|AlphaNum|Min:6|Confirmed',
						'password_confirmation'	=> 'Required|AlphaNum|Min:6',
						'last_name'     		=> 'required',
						'first_name'    		=> 'required'
				);
		
				$messages = [
						'required'		=> 'El campo :attribute es requerido!',
						'unique'		=> 'El campo :attribute ya existe, no se admiten duplicados!',
						'size:3'		=> 'El campo :attribute queriere un mínimo de tres dígitos!',
						'between:6,50'	=> 'El campo :attribute debe tener de seis a cincuenta dígitos!',
						'between:6,10'	=> 'El campo :attribute debe tener de seis a diez!',
						'alphanum'		=> 'El campo :attribute debe ser autonumérico!',      
						'email'			=> 'El campo :attribute debe ser un email válido!'   
				];        
						
				$validation = \Validator::make($input, $rules, $messages);      	
				//dd($validation);
		
		if ($validation->passes())
		{

			$user = new User;
			$user->username      	= Input::get('username');
			$user->email     		= Input::get('email');
			$user->password      	= bcrypt(Input::get('password'));
			$user->first_name 		= (string)Input::get('first_name');		
			$user->last_name 		= (string)Input::get('last_name');
			$user->middle_name   	= (string)Input::get('middle_name');			
			$user->sur_name			= (string)Input::get('sur_name');
			$user->telefono      	= Input::get('telefono');			
			$user->celular       	= Input::get('celular');			
			$user->activated     	= '0';
			$user->save();

			//registra al nuevo usuario como invitado
			$ug = new Role;
			$ug->role_id       	= '5';
			$ug->user_id     	= $user->id;
			$ug->save();	 

			// Actualiza la ruta de la imagen del Administrador
			$img_path = User::find($user->id);
			$img_path->imagen = "assets/img/users/user_".$user->id.".jpg";
			$img_path->save();		
			
			// Registra en bitacoras
			$detalle =	'username= '.		Input::get('username'). ', '.
						'email= '.		    Input::get('email'). ', '.
						'first_name= '.	    Input::get('first_name'). ', '.						
						'last_name= '.	    Input::get('last_name'). ', '.
						'middle_name= '.	Input::get('middle_name'). ', '.
						'sur_name= '.	    Input::get('sur_name'). ', '.						
						'telefono= '.		Input::get('telefono'). ', '.
						'celular= '.		Input::get('celular');
			
			Sity::RegistrarEnBitacora(1, 'users', $user->id, $detalle);
			Session::flash('success', 'El Usuario con el email ' .Input::get('email'). ' ha sido registrado en la base de datos de Sityweb. Por favor, escriba su email y su clave para ingresar al sistema. Gracias por visitar Sityweb.');
			return redirect()->route('users.index');
		}
				return back()->withInput()->withErrors($validation);
	}*/

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario para edición
	 ************************************************************************************/	
	public function edit($user_id)
	{
		return view('core.users.edit')
				->with('dato', User::find($user_id));
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
					'username'    => 'required',
					'email'    		=> 'required',
					'first_name' 	=> 'required',
					'last_name'		=> 'required',
					'cedula'			=> 'required'
			);

			$messages = [
					'required' => 'El campo :attribute es requerido!',
					'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				
				$dato = User::find($id);
				$dato->username      = Input::get('username');
				$dato->email       	 = Input::get('email');
				$dato->first_name    = Input::get('first_name');			
				$dato->last_name     = Input::get('last_name');
				$dato->middle_name   = Input::get('middle_name');			
				$dato->sur_name      = Input::get('sur_name');
				$dato->cedula        = Input::get('cedula');
				$dato->telefono      = Input::get('telefono');			
				$dato->celular       = Input::get('celular');			
				$dato->activated     = Input::has('activated');		
				Sity::RegistrarEnBitacora($dato, Input::get(), 'User', 'Actualiza Usuario');
				$dato->save();		
			
				DB::commit();
				
				Session::flash('success', 'El Usuario "'.$dato->username.'" ha sido editado con éxito.');
				return redirect()->route('users.index');
			}
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrió un error en UnsController.update, la transacción ha sido cancelada!');
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
			//dd($id);
			/*No se permitirá borrar aquellos blqadmins que cumplan con por lo menos una de siguientes condiciones:
				1. Usuarios que estén asignados a un bloque administrativo.
				2. Usuarios que estén asignados como propietarios de al menos una Unidad.*/

			$dato = Blqadmin::where('user_id', $id)->first();
			if(!empty($dato)) {
				
				Session::flash('warning', 'El Usuario no se puede borrar porque es Administrador de Bloque.');
				return redirect()->route('users.index');
			}
			
			$dato = Prop::where('user_id', $id)->first();
			if(!empty($dato)) {
				
				Session::flash('warning', 'El Usuario no se puede borrar porque es Propietario de Unidad.');
				return redirect()->route('users.index');
			}

			$user = User::find($id);
  		$user->delete();

  		Sity::RegistrarEnBitacora($user, Null, 'User', 'Elimina Usuario');  
			DB::commit();
			
			Session::flash('success', 'El Administrador de Phs ' .$user->FullName. ' ha sido borrado permanentemente de la base de datos.');
			return redirect()->route('users.index');

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en UnsController.destroy, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
	}

	//=====================================================================================
	//= Funciones especiales del controlador
	//=====================================================================================    
	
	/*************************************************************************************
	 * Sube una imagen a la carpeta de phadmins
	 ************************************************************************************/	
	public function subirImagenUser($id)
	{
			
		DB::beginTransaction();
		try {
			$input = Input::all();
			$rules = array(
				'file' => 'required|image|max:10000|mimes:jpeg,jpg,gif,png,bmp'
			);

			$messages = array(
				'required' 	=> 'Debe seleccionar una imagen',
				'image' 		=> 'El archivo no es una imagen',
				'max' 			=> 'La imagen sobrepasa el tamaño máximo de 300',
				'mimes' 		=> 'La imagen deberá tener una de las siguienes extensiones jpg,gif,png,bmp'
			);

			$validation = Validator::make($input, $rules, $messages);
			if ($validation->fails())
			{
				return back()->withInput()->withErrors($validation);
			}

			$file = Input::file('file'); 
			$destinationPath = "assets/img/users";
			$filename = "user-L_".$id.".jpg";

			$uploadSuccess = Input::file('file')->move($destinationPath, $filename);
			if( $uploadSuccess ) {
				// Actualiza la ruta de la imagen del nuevo producto
				$img_path = User::find($id);
				$img_path->imagen_L = "assets/img/users/user-L_".$id.".jpg";
				$img_path->imagen_M = "assets/img/users/user-M_".$id.".jpg";
				$img_path->imagen_S = "assets/img/users/user-S_".$id.".jpg";
				$img_path->save();
				
				// crea imagen normal resize the image
				$img = Image::make($img_path->imagen_L)->resize(900, 500);
				File::delete($img_path->imagen_L);					
				$img->save("assets/img/users/user-L_".$id.".jpg");
				
				// crea thumpnail No 1
				$img = Image::make($img_path->imagen_L)->resize(189, 189);
				File::delete($img_path->imagen_M);				
				$img->save("assets/img/users/user-M_".$id.".jpg");

				// crea thumpnail No 2 
				$img = Image::make($img_path->imagen_L)->resize(90, 90);
				File::delete($img_path->imagen_S);				
				$img->save("assets/img/users/user-S_".$id.".jpg");			
				
				// Registra en bitacoras
				//$detalle =	'Usuario cambió la imagen del producto';       
				
				//Sity::RegistrarEnBitacora(2, 'users', $id, $detalle);
				DB::commit();
				
				Session::flash('success', 'La imagen se actualizó con éxito.');
				return back()->withInput();
			}
			else {
				Session::flash('danger', 'La imagen no se pudo subir.');
				return back()->withInput();
			}

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrió un error en UnsController.subirImagen, la transacción ha sido cancelada!');
			return back()->withInput();
		}
	}

}