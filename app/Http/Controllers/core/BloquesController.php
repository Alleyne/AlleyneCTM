<?php

namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;
use Image;
use Cache;

use App\Jd;
use App\Bitacora;
use App\Bloque;
use App\User;
use App\Blqadmin;
use App\Seccione;

class BloquesController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega todos los bloques que pertenecen a un determinada Junta Diretiva.
     ************************************************************************************/	
	public function indexblqplus()
	{
		if (Cache::get('esAdminkey') || Cache::get('esJuntaDirectivakey')) {
		    //Obtiene los datos del la Justa Directiva y de todos los bloques registrados
		    $jd = Jd::first();
		    $bloques = Bloque::All();
			//dd($jd->toArray(), $bloques->toArray());
	    } else {
		    //Obtiene los datos del la Justa Directiva	    
		    $jd = Jd::first();
		    
		    //Obtiene los bloques que pertenecen a un determinado blqadmin	    
			$bloques = Bloque::join('blqadmins', 'blqadmins.bloque_id', '=', 'bloques.id')
			  		 ->where('blqadmins.user_id', Auth::user()->id)
				 	 ->select('bloques.id','bloques.nombre')
				 	 ->get();
	    	//dd($jd->toArray(), $bloques->toArray());
	    }
	    
	    return view('core.bloques.indexblqplus')->with('jd', $jd)
	    										   ->with('bloques', $bloques);

	    /*----------------------------------------------------------------------------------
	    api setup
	    $datos = Jd::find($jd_id)->load('bloques');
        return response()->json($datos->toArray()); 
        -----------------------------------------------------------------------------------*/
	}	

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario sólo lectura
	 ************************************************************************************/	
	public function showblqplus($bloque_id)
	{
	    //dd($bloque_id);
	    //Obtiene los datos del la Justa Directiva con el respectivo bloques_id	    
	    $bloque = Bloque::find($bloque_id);
	    $jd = Jd::find($bloque->jd_id);		
		$blqadmins=Blqadmin::join('users', 'users.id', '=', 'blqadmins.user_id')
		  		->where('blqadmins.bloque_id', '=', $bloque->id)
			 	->get();
		//dd($jd->toArray(), $bloque->toArray(), $blqadmins->toArray());
	    
	    return view('core.bloques.showblqplus')->with('jd', $jd)
	    										  ->with('bloque', $bloque)
												  ->with('blqadmins', $blqadmins);
    
	    /*----------------------------------------------------------------------------------
	    api setup 
	   	$datos = Jd::find($jd_id)->load(['bloques' => function ($query) use ($bloque_id) {
		    $query->where('id', $bloque_id);
		}]);
        return response()->json($datos->toArray());
        -----------------------------------------------------------------------------------*/
	}

	/*************************************************************************************
	 * Despliega formulario para crear un nuevo registro
	 ************************************************************************************/	
	public function createblq($jd_id)
	{
	    return view('core.bloques.createblq')
					->with('jd_id', $jd_id);        			
	} 

    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $rules = array(
            'nombre'    	=> 'required',
         	'codigo'    	=> 'Required|Min:4|Max:4|Alpha_num',
            'descripcion' 	=> 'required'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = new Bloque;
			$dato->nombre         = Input::get('nombre');
			$dato->codigo         = strtoupper(Input::get('codigo'));
			$dato->descripcion    = Input::get('descripcion');
		    $dato->jd_id 	  	  = Input::get('jd_id'); 
		    $dato->save(); 
			
			// agrega un administrador temporal
			$blqadmin = new Blqadmin;
			$blqadmin->bloque_id         = $dato->id;			
			$blqadmin->user_id  		 = Auth::user()->id;
			$blqadmin->cargo             = 0;					
			$blqadmin->encargado		 = 1;			
			$blqadmin->save();

			// Actualiza la ruta de la imagen del Administrador
			$img_path = Bloque::find($dato->id);
			$img_path->imagen_L = "assets/img/bloques/bloq_L".$dato->id.".jpg";
			$img_path->imagen_M = "assets/img/bloques/bloq-M".$dato->id.".jpg";
			$img_path->imagen_S = "assets/img/bloques/bloq-S".$dato->id.".jpg";

			$img_path->save();			
			
			// Registra en bitacoras
			$detalle =	'Crea Bloque '. $dato->nombre. 'codigo '.$dato->codigo.', con la siguiente descripcion: '.  $dato->descripcion;  
			
			Sity::RegistrarEnBitacora(1, 'bloques', $dato->id, $detalle);
			Session::flash('success', 'El Bloque administrativo ' .$dato->nombre. ' ha sido creado con éxito.');
		    return Redirect::route('indexblqplus', Input::get('jd_id'));
		}

        return Redirect::back()->withInput()->withErrors($validation);
	}

    /*************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     *************************************************************************/	
	public function edit($bloque_id)
	{
		return view('core.bloques.edit')
			 ->with('bloque', Bloque::find($bloque_id));
	}

	/*************************************************************************
	 * Actualiza registro
	 ************************************************************************/
	public function update($id)
	{
	    //dd(Input::get());
	    $input = Input::all();
	    $rules = array(
	        'nombre'    	=> 'required',
	        'descripcion' 	=> 'required'
	    );

	    $messages = [
	        'required' => 'El campo :attribute es requerido!',
	        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
	    ];        
	        
	    $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = Bloque::find($id);
			$dato->nombre       	= Input::get('nombre');
			$dato->descripcion      = Input::get('descripcion');
			$dato->save();			
			//dd($dato->toArray());
			
			// Registra en bitacoras
			$detalle =	'nombre= '.		   $dato->nombre. ', '.
						'descripcion= '.   $dato->descripcion;  
			
			Sity::RegistrarEnBitacora(2, 'bloques', $dato->id, $detalle);
			Session::flash('success', 'El Bloque administrativo No. ' .$id. ' ha sido editado con éxito.');
		    
			return Redirect::route('indexblqplus', $dato->jd_id);
		}
	    return Redirect::back()->withInput()->withErrors($validation);
	}

    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($bloque_id)
	{
		//dd($bloque_id);
		/*No se permitirá borrar aquellos Bloques administrativos que cumplan con por lo menos una de siguientes condiciones:
			1. Que tenga por lo menos una sección asigna al mismo.
			2. Que tenga por lo menso un Administrador asignado al mismo.*/
		
		$bloque = Bloque::find($bloque_id);
		
		$secciones = Seccione::where('bloque_id', $bloque_id)->first();
		//dd($secciones);
		
		// Revisa si hay algún usuario vinculado al bloque
		$users= Blqadmin::where('bloque_id', $bloque_id)
							   	   ->first();		
		
		if(!empty($users)) {
			Session::flash('success', 'El Bloque administrativo ' .$bloque->nombre. ' no puede ser borrado porque tiene por lo menos un administrador vinculado al mismo.');
			return Redirect::back();
		}
		
		elseif(!empty($secciones)) {
			Session::flash('success', 'El Bloque administrativo ' .$bloque->nombre. ' no puede ser borrado porque tiene por lo menos una sección asignada al mismo.');
			return Redirect::back();
		}
		
		else {

			$bloque->delete();

			// Registra en bitacoras
			$detalle =	'Borra el Bloque '. $bloque->nombre. ', con la siguiente descripcion: '.  $bloque->descripcion;       
			
			Sity::RegistrarEnBitacora(3, 'bloques', $bloque->id, $detalle);
			Session::flash('success', 'El Bloque administrativo ' .$bloque->nombre. ' ha sido borrado permanentemente de la base de datos.');			
			return Redirect::back();
		}
	}

   /*************************************************************************************
     * Sube una imagen a la carpeta de bloques
     ************************************************************************************/	
	public function subirImagenBloque($id)
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
        $destinationPath = "assets/img/bloques";
        $filename = "bloq-L".$id.".jpg";

        $uploadSuccess = Input::file('file')->move($destinationPath, $filename);
        if( $uploadSuccess ) {
			// Actualiza la ruta de la imagen del nuevo producto
			$img_path = Bloque::find($id);
			$img_path->imagen_L = "assets/img/bloques/bloq-L".$id.".jpg";
			$img_path->imagen_M = "assets/img/bloques/bloq-M".$id.".jpg";
			$img_path->imagen_S = "assets/img/bloques/bloq-S".$id.".jpg";
			$img_path->save();
			
			// crea imagen normal
			// resize the image to a height of 300 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 500, true);
			$img->save("assets/img/bloques/bloq-L".$id.".jpg");
			
			// crea thumpnail No 1
			// resize the image to a height of 189 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(189, null, true);
			$img->save("assets/img/bloques/bloq-M".$id.".jpg");

			// crea thumpnail No 2 
			// resize the image to a height of 90 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 90, true);
			$img->save("assets/img/bloques/bloq-S".$id.".jpg");			
			
			// Registra en bitacoras
			$detalle =	'Usuario cambió la imagen del bloque';       
			
			Sity::RegistrarEnBitacora(2, 'bloques', $id, $detalle);
			Session::flash('success', 'La imagen se actualizó con éxito.');
			return Redirect::back()->withInput();
		}
		else {
        	Session::flash('danger', 'La imagen no se pudo subir.');
			return Redirect::back()->withInput();
		}
	}

} 