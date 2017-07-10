<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Session, Validator,Image, DB;
use App\library\Sity;
use App\Http\Helpers\Grupo;

use App\Bitacora;
use App\Jd;
use App\Bloque;

class JdsController extends Controller {
		
	public function __construct()
	{
			$this->middleware('hasAccess');    
	}
		
	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function index()
	{
		//Obtiene todos las Jds actualmente registrados en la base de datos.
		$datos = Jd::orderBy('nombre', 'asc')->get();
		//dd($datoss->toArray());

		return view('core.jds.index')->with('datos', $datos);     	
	}	

		/*************************************************************************************
		 * Despliega el registro especificado en formato formulario sólo lectura
		 ************************************************************************************/	
	public function show($id)
	{

		$dato = Jd::find($id);
		if(!empty($dato)) {
			return view('core.jds.show')->with('dato', $dato);
		
		} else {
			Session::flash('danger', 'La Junta directiva No. ' .$id. ' no existe.');
			return redirect()->route('jds.index');	    	
		}
	}
	 
	 /*************************************************************************************
		 * Despliega formulario para crear un nuevo registro
		 ************************************************************************************/	
	public function create()
	{
		return view('core.jds.create');
	}     
		
		/*************************************************************************************
		 * Almacena un nuevo registro en la base de datos
		 ************************************************************************************/	
	public function store()
	{
		//dd(Input::all());
			
	  DB::beginTransaction();
	  try {

			$input = Input::all();
			$rules = array(
					'nombre'    => 'required',
					'codigo'    => 'Required|Min:6|Max:6|Alpha'
			);

			$messages = [
					'required' => 'El campo :attribute es requerido!',
					'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				
				$dato = new Jd;
				
				$dato->nombre       	= Input::get('nombre');
				$dato->codigo		    	= strtoupper(Input::get('codigo'));
				$dato->pais 	       	= Input::get('pais');
				$dato->provincia	   	= Input::get('provincia');
				$dato->distrito      	= Input::get('distrito');
				$dato->corregimiento	= Input::get('corregimiento');
				$dato->comunidad      = Input::get('comunidad');
				$dato->calle 		    	= Input::get('calle');
				$dato->telefono       = Input::get('telefono');
				$dato->celular	      = Input::get('celular');
				$dato->email 	       	= Input::get('email');
				
				$dato->save();	
				
				// Actualiza la ruta de la imagen del Administrador
				$img_path = Jd::find($dato->id);
				$img_path->imagen_L = "assets/img/jds/jd_".$dato->id.".jpg";
				$img_path->save();			

				Sity::RegistrarEnBitacora($dato, Input::get(), 'Jd', 'Crea nueva Junta directiva');
				Session::flash('success', 'La Junta directiva No. ' .$dato->nombre. ' ha sido creada con éxito.');
	  		
	  		DB::commit();
				return redirect()->route('jds.index');
			}
			return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en JdController@store, la transaccion ha sido cancelada!');
	    return back()->withInput();
	  }	
	}
		
		
	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario para edición
	 ************************************************************************************/	
	public function edit($id)
	{
		return view('core.jds.edit')->with('dato', Jd::find($id));
	}


	/*************************************************************************************
	 * Actualiza registro
	 ************************************************************************************/
	public function update($id)
	{
		//dd(Input::get());

	  DB::beginTransaction();
	  try {

			$input = Input::all();
			$rules = array(
				'nombre'    => 'required',
				'codigo'    => 'Required|Min:6|Max:6|Alpha'
			);

			$messages = [
					'required' => 'El campo :attribute es requerido!',
					'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				$dato = Jd::find($id);
				$dato->nombre       	= Input::get('nombre');
				$dato->codigo	       	= Input::get('codigo');
				$dato->pais 	       	= Input::get('pais');
				$dato->provincia	    = Input::get('provincia');
				$dato->distrito       = Input::get('distrito');
				$dato->corregimiento	= Input::get('corregimiento');
				$dato->comunidad     	= Input::get('comunidad');
				$dato->calle 		    	= Input::get('calle');
				$dato->telefono       = Input::get('telefono');
				$dato->celular	      = Input::get('celular');
				$dato->email 	       	= Input::get('email');
				$dato->save();			
				
				Sity::RegistrarEnBitacora($dato, Input::get(), 'Jd', 'Actualiza Junta directiva');
				Session::flash('success', 'La Junta directiva ' .$dato->nombre. ' ha sido editada con éxito.');
	  		
	  		DB::commit();			
				return redirect()->route('jds.index');
			}
			return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en JdController@update, la transaccion ha sido cancelada!');
	    return back()->withInput();
	  }
	}


	
  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/		
	public function destroy($jd_id)
	{
		//dd($jd_id);
		/*
		No se permitirá borrar aquellas Juntas directivas que cumplan con por lo menos una de siguientes condiciones:
		1. Juntas directivas que tengan por lo menos un Bloque asignado a la misma.*/
		
	  DB::beginTransaction();
	  try {
			$dato = Jd::find($jd_id);
			
			// Revisa si hay algún bloque asignado a la junta directiva
			$bloques= Bloque::where('jd_id', $jd_id)->first();
			//dd($bloques);

			if(!empty($bloques)) {
				Session::flash('warning', 'La Junta Directiva ' .$dato->nombre. ' no puede ser borrada porque tiene uno o más bloques asignadoa a la misma.');
				return redirect()->route('jds.index');	
			}
			
			else {
				$dato->delete();

	  		Sity::RegistrarEnBitacora($dato, Null, 'Jd', 'Elimina Junta directiva');   
				Session::flash('success', 'La Junta Directiva ' .$dato->nombre. ' ha sido borrada permanentemente de la base de datos.');			
	  		
	  		DB::commit();				
				return redirect()->route('jds.index');	
			}

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en JdController@destroy, la transaccion ha sido cancelada!');
	    return back()->withInput();
	  }		
	}

		
	//=====================================================================================
	//= Funciones especiales del controlador
	//=====================================================================================    
	
	/*************************************************************************************
	 * Sube una imagen a la carpeta de jds
	 ************************************************************************************/	
	public function subirImagenJd($id)
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
		if ($validation->fails()) {
			return back()->withInput()->withErrors($validation);
		}

		$file = Input::file('file'); 
		$destinationPath = "assets/img/jds";
		$filename = "jd-L".$id.".jpg";

		$uploadSuccess = Input::file('file')->move($destinationPath, $filename);
		if( $uploadSuccess ) {
			// Actualiza la ruta de la imagen del nuevo producto
			$img_path = Jd::find($id);
			$img_path->imagen_L = "assets/img/jds/jd-L".$id.".jpg";
			$img_path->imagen_M = "assets/img/jds/jd-M".$id.".jpg";
			$img_path->imagen_S = "assets/img/jds/jd-S".$id.".jpg";
			$img_path->save();
			
			// crea imagen normal
			// resize the image to a height of 300 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(900, 500);
			$img->save("assets/img/jds/jd-L".$id.".jpg");
			
			// crea thumpnail No 1
			// resize the image to a height of 189 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(189, 189);
			$img->save("assets/img/jds/jd-M".$id.".jpg");

			// crea thumpnail No 2 
			// resize the image to a height of 90 and constrain aspect ratio (auto width)
			$img = Image::make($img_path->imagen_L)->resize(90, 90);
			$img->save("assets/img/jds/jd-S".$id.".jpg");			
			
			//Sity::RegistrarEnBitacora(2, 'jds', $id, $detalle);
			
			Session::flash('success', 'La imagen se actualizó con éxito.');
			return back()->withInput();
		
		} else {
			Session::flash('danger', 'La imagen no se pudo subir.');
			return back()->withInput();
		}
	}
} 