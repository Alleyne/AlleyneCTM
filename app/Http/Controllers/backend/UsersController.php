<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Redirect, Session;
use Role;
use Validator;
use Image;
use App\Hash;

use App\User;
use App\Bitacora;

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
  		
  		return view('backend.users.index')->with('datos', $datos);
	}	

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario sólo lectura
     ************************************************************************************/	
	public function show($id)
	{
	    $dato = User::find($id);
	    //dd($dato);
	    
	    if(!empty($dato)) {
			return view('backend.users.show')->with('dato', $dato);
		}
	    else {
			Session::flash('danger', 'El Usuario No. ' .$id. ' no existe.');
			return Redirect::route('backend.users.show');	    	
	    }
	}
    
   /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ***********************************************************************************	
	public function create()
	{
        return view('backend.users.create');
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
			return Redirect::route('users.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}*/

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     ************************************************************************************/	
	public function edit($user_id)
	{
		return view('backend.users.edit')
				->with('dato', User::find($user_id));
	}

    /*************************************************************************************
     * Actualiza registro
     ************************************************************************************/
	public function update($id)
	{
        //dd(Input::get());
        $input = Input::all();
        $rules = array(
            'username'      => 'required',
            'email'    => 'required',
            'first_name' 	=> 'required',
            'last_name'	=> 'required',
            'cedula'	=> 'required'
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
			$dato->save();		
		
			// Registra en bitacoras
			$detalle =	'username= '.		$dato->username. ', '.
						'email= '.		    $dato->email. ', '.
						'first_name= '.	    $dato->first_name. ', '.						
						'last_name= '.	    $dato->last_name. ', '.
						'middle_name= '.	$dato->middle_name. ', '.
						'sur_name= '.	    $dato->sur_name. ', '.						
						'telefono= '.		$dato->telefono. ', '.
						'cedula= '.		    $dato->cedula. ', '.
						'celular= '.		$dato->celular;      
			
			Sity::RegistrarEnBitacora(2, 'users', $dato->id, $detalle);
			Session::flash('success', 'El Usuario No. ' .$id. ' ha sido editado con éxito.');
			return Redirect::route('users.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
  	}
  
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($id)
	{
		//dd($id);
		/*No se permitirá borrar aquellos blqadmins que cumplan con por lo menos una de siguientes condiciones:
			1. blqadmins que estén asignados a un bloque administrativo.
			2. blqadmins que estén asignados como propietarios de al menos una Unidad.*/

		$dato = Users_group::where('user_id','=', $id)->first();
		if(!empty($dato)) {
			
			Session::flash('warning', 'El Usuario No. ' .$id. ' no se puede borrar porque tiene registros relacionados.');
			return Redirect::route('admin.users.index');
		}
		
		$dato = Phadmin::find($id);
		//$dato->delete();

		// Registra en bitacoras
		$detalle =	'username= '.		$dato->tipo. ', '.
					'email= '.		    $dato->nombre. ', '.
					'password= '.		$dato->codigo. ', '.  
					'first_name= '.	    $dato->digitov. ', '.						
					'last_name= '.	    $dato->direccion. ', '.
					'middle_name= '.	$dato->pais. ', '.
					'sur_name= '.	    $dato->provincia. ', '.						
					'telefono= '.		$dato->imagen. ', '.
					'celular= '.		$dato->imagen; 
		
		Sity::RegistrarEnBitacora(3, 'phadmins', $dato->id, $detalle);
		Session::flash('success', 'El Administrador de Phs ' .$dato->nombre. ' ha sido borrado permanentemente de la base de datos.');
		return Redirect::route('users.index');
	}

    //=====================================================================================
    //= Funciones especiales del controlador
    //=====================================================================================    
    
    /*************************************************************************************
     * Sube una imagen a la carpeta de phadmins
     ************************************************************************************/	
	public function subirImagen($id)
	{
        $input = Input::all();
        $rules = array(
       		'file' => 'required|image|max:10000|mimes:jpeg,jpg,gif,png,bmp'
        );

		$messages = array(
		    'required' => 'Debe seleccinar una imagen',
		    'image' => 'El archivo no es una imagen',
		    'max' => 'La imagen sobrepasa el tamaño máximo de 300',
		    'mimes' => 'La imagen deberá tener una de las siguienes extensiones jpg,gif,png,bmp'
        );

        $validation = Validator::make($input, $rules, $messages);
        if ($validation->fails())
        {
        	return Redirect::back()->withInput()->withErrors($validation);
        }

        $file = Input::file('file'); 
        $destinationPath = "assets/img/phadmins";
        $filename = "adm-L_".$id.".jpg";

        $uploadSuccess = Input::file('file')->move($destinationPath, $filename);
        if( $uploadSuccess ) {
			// Actualiza la ruta de la imagen del nuevo producto
			$img_path = User::find($id);
			$img_path->imagen_L = "assets/img/phadmins/adm-L_".$id.".jpg";
			$img_path->imagen_M = "assets/img/phadmins/adm-M_".$id.".jpg";
			$img_path->imagen_S = "assets/img/phadmins/adm-S_".$id.".jpg";
			$img_path->save();
			
			// crea imagen normal
			// resize the image to a height of 300 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 500, true);
			$img->save("assets/img/phadmins/adm-L_".$id.".jpg");
			
			// crea thumpnail No 1
			// resize the image to a height of 189 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(189, null, true);
			$img->save("assets/img/phadmins/adm-M_".$id.".jpg");

			// crea thumpnail No 2 
			// resize the image to a height of 90 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 90, true);
			$img->save("assets/img/phadmins/adm-S_".$id.".jpg");			
			
			// Registra en bitacoras
			$detalle =	'Usuario cambió la imagen del producto';       
			
			Sity::RegistrarEnBitacora(2, 'users', $id, $detalle);
			Session::flash('success', 'La imagen se actualizó con éxito.');
			return Redirect::back()->withInput();
		}
		else {
        	Session::flash('danger', 'La imagen no se pudo subir.');
			return Redirect::back()->withInput();
		}
	}
}