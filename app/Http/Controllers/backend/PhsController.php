<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;
use Image;

use App\User;
use App\Bitacora;
use App\Ph;
use App\Seccione;

class PhsController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function index()
	{
        //Obtiene todos los Phs actualmente registrados en la base de datos.
        $datos = Ph::orderBy('nombre', 'asc')->get();
        //dd($datoss->toArray());
  		
  		return view('backend.phs.index')->with('datos', $datos);     	
	}	

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario sólo lectura
     ************************************************************************************/	
	public function show($id)
	{

	    $dato = Ph::find($id);
	    if(!empty($dato)) {
			return view('backend.phs.show')->with('dato', $dato);
		}
	    else {
			Session::flash('danger', 'El Ph backendistrativo No. ' .$id. ' no existe.');
			return Redirect::route('phs.index');	    	
	    }
	}
   
   /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function create()
	{
        return view('backend.phs.create');
	}     
    
    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $rules = array(
            'tipo'			=> 'required',
            'nombre'    	=> 'required',
        	'codigo'    	=> 'Required|Min:6|Max:6|Alpha'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
			$dato = new Ph;
			$dato->nombre       	= Input::get('nombre');
			$dato->codigo		    = strtoupper(Input::get('codigo'));
			$dato->tipo			    = Input::get('tipo');
			$dato->pais 	       	= Input::get('pais');
			$dato->provincia	    = Input::get('provincia');
			$dato->distrito       	= Input::get('distrito');
			$dato->corregimiento    = Input::get('corregimiento');
			$dato->comunidad       	= Input::get('comunidad');
			$dato->calle 		    = Input::get('calle');
			$dato->telefono       	= Input::get('telefono');
			$dato->celular	        = Input::get('celular');
			$dato->email 	       	= Input::get('email');
			$dato->save();	
			
			// Actualiza la ruta de la imagen del Administrador
			$img_path = Ph::find($dato->id);
			$img_path->imagen_L = "assets/img/phs/bloq_".$dato->id.".jpg";
			$img_path->save();			
			
			// Registra en bitacoras
			$detalle =	'nombre= '.		    $dato->nombre. ', '.
						'codigo= '.   		$dato->codigo. ', '.
						'tipo= '.   		$dato->descripcion. ', '.
						'pais= '. 			$dato->pais. ', '.
						'provincia= '. 		$dato->provincia. ', '.
						'distrito= '. 		$dato->distrito. ', '.
						'corregimiento= '. 	$dato->corregimiento. ', '.
						'comunidad= '. 		$dato->comunidad. ', '.
						'calle= '. 			$dato->calle. ', '.
						'telefono= '. 		$dato->telefono. ', '. 
						'celular= '. 		$dato->celular. ', '.
						'email= '. 			$dato->email;  
    
			Sity::RegistrarEnBitacora(1, 'phs', $dato->id, $detalle);
			Session::flash('success', 'El Ph administrativo No. ' .$dato->id. ' ha sido creado con éxito.');

			return Redirect::route('phs.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}
    
    
    /*************************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     ************************************************************************************/	
	public function edit($id)
	{
		return view('backend.phs.edit')->with('dato', Ph::find($id));
	}


    /*************************************************************************************
     * Actualiza registro
     ************************************************************************************/
	public function update($id)
	{
        //dd(Input::get());
        $input = Input::all();
        $rules = array(
            'tipo'			=> 'required',
            'nombre'    	=> 'required'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = Ph::find($id);
			$dato->nombre       	= Input::get('nombre');
			$dato->tipo			    = Input::get('tipo');
			$dato->pais 	       	= Input::get('pais');
			$dato->provincia	    = Input::get('provincia');
			$dato->distrito       	= Input::get('distrito');
			$dato->corregimiento    = Input::get('corregimiento');
			$dato->comunidad       	= Input::get('comunidad');
			$dato->calle 		    = Input::get('calle');
			$dato->telefono       	= Input::get('telefono');
			$dato->celular	        = Input::get('celular');
			$dato->email 	       	= Input::get('email');
			$dato->save();			
			
			// Registra en bitacoras
			$detalle =	'nombre= '.		    $dato->nombre. ', '.
						'tipo= '.   		$dato->descripcion. ', '.
						'pais= '. 			$dato->pais. ', '.
						'provincia= '. 		$dato->provincia. ', '.
						'distrito= '. 		$dato->distrito. ', '.
						'corregimiento= '. 	$dato->corregimiento. ', '.
						'comunidad= '. 		$dato->comunidad. ', '.
						'calle= '. 			$dato->calle. ', '.
						'telefono= '. 		$dato->telefono. ', '. 
						'celular= '. 		$dato->celular. ', '.
						'email= '. 			$dato->email;

			Sity::RegistrarEnBitacora(2, 'phs', $dato->id, $detalle);
			Session::flash('success', 'El Ph administrativo No. ' .$id. ' ha sido editado con éxito.');
			
			return Redirect::route('phs.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
  	}
  
  
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($ph_id)
	{
		//dd($id);
		/* No se permitirá borrar aquellos Phs que cumplan con por lo menos una de siguientes condiciones:
			1. Phs que estén asignados a una o varias secciones. */
		
		$dato = Ph::find($ph_id);		
		
		// Revisa si hay alguna unidad asignada a la junta directiva
		$seccion= Seccione::where('ph_id', $ph_id)->first();
		//dd($seccion);

		if(!empty($seccion)) {
			Session::flash('success', 'El PH ' .$dato->nombre. ' no puede ser borrado porque está asignado a una varias Secciones Administrativas.');
		}
		
		else {
			
			$dato->delete();			

			// Registra en bitacoras
			$detalle =	'Borra el Ph '.	    $dato->nombre. ', '.
						'tipo= '.   		$dato->descripcion. ', '.
						'pais= '. 			$dato->pais. ', '.
						'provincia= '. 		$dato->provincia. ', '.
						'distrito= '. 		$dato->distrito. ', '.
						'corregimiento= '. 	$dato->corregimiento. ', '.
						'comunidad= '. 		$dato->comunidad. ', '.
						'calle= '. 			$dato->calle. ', '.
						'telefono= '. 		$dato->telefono. ', '. 
						'celular= '. 		$dato->celular. ', '.
						'email= '. 			$dato->email;  
			
			Sity::RegistrarEnBitacora(3, 'phs', $dato->id, $detalle);
			Session::flash('success', 'El Ph administrativo ' .$dato->nombre. ' ha sido borrado permanentemente de la base de datos.');
		}
		return Redirect::route('phs.index');
	}

    //=====================================================================================
    //= Funciones especiales del controlador
    //=====================================================================================    
    
    /*************************************************************************************
     * Sube una imagen a la carpeta de phs
     ************************************************************************************/	
	public function subirImagenPh($id)
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
        $destinationPath = "assets/img/phs";
        $filename = "bloq-L".$id.".jpg";

        $uploadSuccess = Input::file('file')->move($destinationPath, $filename);
        if( $uploadSuccess ) {
			// Actualiza la ruta de la imagen del nuevo producto
			$img_path = Ph::find($id);
			$img_path->imagen_L = "assets/img/phs/bloq-L".$id.".jpg";
			$img_path->imagen_M = "assets/img/phs/bloq-M".$id.".jpg";
			$img_path->imagen_S = "assets/img/phs/bloq-S".$id.".jpg";
			$img_path->save();
			
			// crea imagen normal
			// resize the image to a height of 300 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 500, true);
			$img->save("assets/img/phs/bloq-L".$id.".jpg");
			
			// crea thumpnail No 1
			// resize the image to a height of 189 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(189, null, true);
			$img->save("assets/img/phs/bloq-M".$id.".jpg");

			// crea thumpnail No 2 
			// resize the image to a height of 90 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(null, 90, true);
			$img->save("assets/img/phs/bloq-S".$id.".jpg");			
			
			// Registra en bitacoras
			$detalle =	'Usuario cambió la imagen del ph';       
			
			Sity::RegistrarEnBitacora(2, 'phs', $id, $detalle);
			Session::flash('success', 'La imagen se actualizó con éxito.');
			return Redirect::back()->withInput();
		}
		else {
        	Session::flash('danger', 'La imagen no se pudo subir.');
			return Redirect::back()->withInput();
		}
	}
} 