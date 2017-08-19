<?php

namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, Validator, Image, Cache, DB, File;
use App\library\Sity;

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
		if (Cache::get('esAdminkey') || Cache::get('esJuntaDirectivakey') || Cache::get('esContadorkey')) {
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
							 ->select('bloques.id','bloques.codigo','bloques.nombre')
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
	  DB::beginTransaction();
	  try {		
			//dd(Input::all());
			$input = Input::all();
			$rules = array(
				'nombre'    	=> 'required',
				'codigo'    	=> 'Required|Min:4|Max:4|Alpha_num',
				'descripcion'   => 'required'
			);

			$messages = [
				'required' => 'El campo :attribute es requerido!',
				'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				$dato = new Bloque;
				$dato->nombre        = Input::get('nombre');
				$dato->codigo        = strtoupper(Input::get('codigo'));
				$dato->descripcion   = Input::get('descripcion');
				$dato->jd_id 	  	 = Input::get('jd_id'); 
				$dato->save(); 
				
				Sity::RegistrarEnBitacora($dato, Input::get(), 'Bloque', 'Registra nuevo bloque');				
				
				// agrega un administrador temporal
				$blqadmin = new Blqadmin;
				$blqadmin->bloque_id = $dato->id;			
				$blqadmin->user_id   = Auth::user()->id;
				$blqadmin->cargo     = 0;					
				$blqadmin->encargado = 1;			
				$blqadmin->save();

				// Actualiza la ruta de la imagen del Administrador
				//$img_path = Bloque::find($dato->id);
				//$img_path->imagen_L = "assets/img/bloques/bloq_L".$dato->id.".jpg";
				//$img_path->imagen_M = "assets/img/bloques/bloq-M".$dato->id.".jpg";
				//$img_path->imagen_S = "assets/img/bloques/bloq-S".$dato->id.".jpg";
				//$img_path->save();			
				
	  		DB::commit();
				
				Session::flash('success', 'El Bloque administrativo ' .$dato->nombre. ' ha sido creado con éxito.');
				return redirect()->route('indexblqplus', Input::get('jd_id'));
			}
			return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrió un error en BloquesController.store, la transacción ha sido cancelada!');
	    return back()->withInput();
	  }
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
	  DB::beginTransaction();
	  try {
			
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
				
				Sity::RegistrarEnBitacora($dato, Input::get(), 'Bloque', 'Actualiza bloque');
				$dato->save();		  		
	  		
	  		DB::commit();
				
				Session::flash('success', 'El Bloque administrativo No. ' .$id. ' ha sido editado con éxito.');
				return redirect()->route('indexblqplus', $dato->jd_id);
			}
			return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrió un error en BloquesController.update, la transacción ha sido cancelada!');
	    return back()->withInput();
	  }
	}

	/*************************************************************************************
	 * Borra registro de la base de datos
	 ************************************************************************************/	
	public function destroy($bloque_id)
	{
  	DB::beginTransaction();
	  try {
			//dd($bloque_id);
			/*No se permitirá borrar aquellos Bloques administrativos que cumplan con por lo menos una de siguientes condiciones:
				1. Que tenga por lo menos una sección asigna al mismo.
				2. Que tenga por lo menso un Administrador asignado al mismo.*/
			
			$bloque = Bloque::find($bloque_id);
			
			$secciones = Seccione::where('bloque_id', $bloque_id)->first();
			//dd($secciones);
			
			// Revisa si hay algún usuario vinculado al bloque
			$users= Blqadmin::where('bloque_id', $bloque_id)->first();		
			
			if(!empty($users)) {
				Session::flash('warning', 'El Bloque administrativo ' .$bloque->nombre. ' no puede ser borrado porque tiene al menos un administrador vinculado.');
				return back();
			}
			
			elseif(!empty($secciones)) {
				Session::flash('warning', 'El Bloque administrativo ' .$bloque->nombre. ' no puede ser borrado porque tiene al menos una sección asignada.');
				return back();
			}
			
			else {
				$bloque->delete();

				Sity::RegistrarEnBitacora($bloque, Null, 'Bloque', 'Elimina bloque');	
  			DB::commit();
				
				Session::flash('success', 'El Bloque administrativo ' .$bloque->nombre. ' ha sido borrado permanentemente de la base de datos.');			
				return back();
			}

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrió un error en BloquesController.destroy, la transacción ha sido cancelada!');
	    return back();
	  }	

	}

  /*************************************************************************************
	 * Sube una imagen a la carpeta de bloques
	 ************************************************************************************/	
	public function subirImagenBloque($id)
	{
	  DB::beginTransaction();
	  try {
			$input = Input::all();
			$rules = array(
				'file' => 'required|image|max:10000|mimes:jpeg,jpg,gif,png,bmp'
			);

			$messages = array(
					'required' => 'Debe seleccionar una imagen',
					'image' => 'El archivo no es una imagen',
					'max' => 'La imagen sobrepasa el tamaño máximo de 300',
					'mimes' => 'La imagen debe tener una de las siguienes extensiones: jpg,gif,png,bmp'
			);

				$validation = Validator::make($input, $rules, $messages);
				if ($validation->fails()) {
					return back()->withInput()->withErrors($validation);
				}

				$file = Input::file('file'); 
				$destinationPath = "assets/img/bloques";
				$filename = "bloq-L".$id.".jpg";

				$uploadSuccess = Input::file('file')->move($destinationPath, $filename);
				if( $uploadSuccess ) {
					// Actualiza la ruta de la imagen del bloque
					$img_path = Bloque::find($id);
					$img_path->imagen_L = "assets/img/bloques/bloq-L".$id.".jpg";
					$img_path->imagen_M = "assets/img/bloques/bloq-M".$id.".jpg";
					$img_path->imagen_S = "assets/img/bloques/bloq-S".$id.".jpg";
  				//Sity::RegistrarEnBitacora($img_path, $input, 'Bloque', 'Actualiza imagen de bloque');	
					$img_path->save();
					
					// crea imagen normal y resize the image
					$img = Image::make($img_path->imagen_L)->resize(900, 500);
					File::delete($img_path->imagen_L);					
					$img->save("assets/img/bloques/bloq-L".$id.".jpg");
					
					// crea thumpnail No 1
					$img = Image::make($img_path->imagen_L)->resize(189, 189);
					File::delete($img_path->imagen_M);					
					$img->save("assets/img/bloques/bloq-M".$id.".jpg");

					// crea thumpnail No 2 
					$img = Image::make($img_path->imagen_L)->resize(90, 90);
					File::delete($img_path->imagen_S);					
					$img->save("assets/img/bloques/bloq-S".$id.".jpg");			
  			
	  			DB::commit();
					
					Session::flash('success', 'La imagen se actualizó con éxito.');
					return back()->withInput();
				
				} else {
					Session::flash('danger', 'La imagen no se pudo subir.');
					return back()->withInput();
				}

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrió un error en BloquesController.subirImagenBloque, la transacción ha sido cancelada!');
	    return back()->withInput();
	  }
	}

} 