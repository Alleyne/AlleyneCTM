<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Session, DB, Grupo, Validator, Image;
use Carbon\Carbon;

use App\User;
use App\Jd;
use App\Bitacora;
use App\Bloque;
use App\Seccione;
use App\Un;
use App\Org;
use App\Ph;
use App\Seclcre;
use App\Seclced;
use App\Secapto;
use App\Secre;
use App\Blqadmin;

class SeccionesController extends Controller {
		
	public function __construct()
	{
			$this->middleware('hasAccess');    
	}
		
	/*************************************************************************************
	 * Despliega todos las secciones que pertenecen a un determinado Bloque administrativo.
	 ************************************************************************************/    
	public function indexsecplus($bloque_id)
	{
		//dd($bloque_id);
		//Verifica que el presente bloque tenga un administrador encargado.
		//Pueden haber muchos administradores para un bloque, pero solo
		//uno encargado o responsable por el mismo.
			
			//Obtiene los datos del o los administradores del bloque
		$blqadmins = Blqadmin::where('bloque_id', $bloque_id)
							 ->where('encargado','1')
							 ->count();
		//dd($blqadmins);
		
		//verifica que el bloque tenga por lo menos un administrador encargado
		if ($blqadmins == 0) {
			Session::flash('warning', 'Es obligatorio que cada bloque tenga por lo menos un administrador encargado o responsable del mismo. Favor vincular por lo menos un adminstrador ecargado o responsable!');
			return redirect()->route('indexblqadmin', $bloque_id);
		
		} elseif ($blqadmins > 1) {
			Session::flash('warning', 'Un bloque puede tener uno o muchos administradores pero solamente uno puede ser encargado o responsable del mismo. Este bloque tiene mas de uno!.');
			return redirect()->route('indexblqadmin', $bloque_id);
		}
			

		//Obtiene los datos del la Justa Directiva	    
		$jd = Jd::first();

		//Obtiene los datos del Bloques    
		$bloque = Bloque::find($bloque_id);
		//dd($bloque->toArray());
		
		//Obtiene los datos del o los administradores del bloque
		$blqadmins = Blqadmin::where('bloque_id', $bloque->id)
															->with('user')
															->with('org')
															->get();
		//dd($blqadmins->toArray());
		
		//Obtiene todas las Secciones administrativas que pertenecen a un determinado Bloque
		//no importa el tipo de sección.
		$secciones = Seccione::where('bloque_id', $bloque_id)
												 ->orderBy('nombre', 'asc')
												 ->get();
		//dd($secciones->toArray());
			
		return view('core.secciones.indexsecplus')
					->with('jd', $jd)
					->with('bloque', $bloque)
					->with('blqadmins', $blqadmins)
					->with('secciones', $secciones);
	}

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario sólo lectura
	 ************************************************************************************/    
	public function showsecplus($seccione_id)
	{

		//dd($seccione_id);
		//Obtiene datos de la Seccion administrativa que se desea ver no importa el tipo
		$sec=Seccione::with('ph')->find($seccione_id);
		//dd($sec->toArray());

		if (is_null($sec->ph)) {
			Session::flash('warning', 'Es necesario asignar un Ph a cada Sección, favor utilizar el botón Editar para asignarle un Ph a esta Sección!');
					return back();
		}
		
		if ($sec->tipo == 1) {
			$seccion = Seccione::with('secapto')->find($seccione_id); // trae los datos de las dos tablas
		
		} elseif ($sec->tipo == 2) {
				$seccion = Seccione::with('secre')->find($seccione_id);
		
		} elseif ($sec->tipo == 3) {
				$seccion = Seccione::with('seclced')->find($seccione_id);
		
		} elseif ($sec->tipo == 4) {
				$seccion = Seccione::with('seclcre')->find($seccione_id);
		
		} 	
		//dd($seccion->toArray());
		
		//Obtiene los datos del Bloque
		$bloque = Bloque::find($seccion->bloque_id);
		//dd($bloque->toArray());		

		$blqadmins=blqadmin::where('bloque_id', $seccion->bloque_id)
															->with('user')
															->with('org')
															->get();
		//dd($blqadmins->toArray());

		//Obtiene los datos de la Junta Directiva
		$jd = Jd::find($bloque->jd_id);  
	
		return view('core.secciones.showsecplus')
								->with('seccion', $seccion)                
								->with('bloque', $bloque)
								->with('blqadmins', $blqadmins)
								->with('jd', $jd);
	}

  /*************************************************************************************
	 * Despliega formulario para crear un nuevo registro
	 ************************************************************************************/	
	public function createsec($bloque_id, $tipo)
	{
		return view('core.secciones.createsec')
					->with('bloque_id', $bloque_id)
					->with('tipo', $tipo);
	}

  /*************************************************************************************
	 * Almacena un nuevo registro en la base de datos
	 ************************************************************************************/	
	public function store()
	{
		DB::beginTransaction();
		try {

			$input = Input::all();
			//dd($input);

			$rules = array(
				'nombre'    			=> 'required',
				'descripcion' 		=> 'required',
				'codigo'    			=> 'Required|Min:2|Max:2|Alpha_num',
				'd_registra_cmpc' => 'required'	    
			);
			
			$messages = [
				'required' => 'El campo :attribute es requerido!',
				'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				// Salva los datos que corresponden a la tabla Secciones
				$dato = new Seccione;
				$dato->tipo 			 = Input::get('tipo');
				$dato->nombre      = Input::get('nombre');
				$dato->codigo      = Input::get('codigo');
				$dato->descripcion = Input::get('descripcion');
				$dato->bloque_id   = Input::get('bloque_id');			
				$dato->save();
				
				// Relaciones de uno a uno con Secciones
				// Salva en la tabla secaptos "Apartamentos en condominios"
				if ($dato->tipo == 1) {  
					$t1 = new Secapto;
					$t1->cuartos               = Input::get('cuartos');
					$t1->banos                 = Input::get('banos');
					$t1->agua_caliente         = Input::get('agua_caliente');            
					$t1->estacionamientos      = Input::get('estacionamientos');
					$t1->cuota_mant            = Input::get('cuota_mant');
					$t1->recargo               = Input::get('recargo');
					$t1->descuento             = Input::get('descuento');								
					$t1->d_registra_cmpc       = Input::get('d_registra_cmpc');
					$t1->d_vence               = Input::get('d_vence');
					$t1->m_descuento           = Input::get('m_descuento');
					$t1->area                  = Input::get('area');
					
					$t1->f_iniciaextra         = Carbon::parse(Input::get('f_iniciaextra'))->startOfMonth();
					$t1->extra_meses           = Input::get('extra_meses');
					$t1->extra                 = Input::get('extra');
					
					$t1->seccione_id           = $dato->id;
					$t1->save();			
				
				}	elseif ($dato->tipo == 2) {  
					// Salva en la tabla secres "Residencias en residenciales"
					$t2 = new Secre;
					$t2->avenida               = Input::get('avenida');
					$t2->cuartos               = Input::get('cuartos');
					$t2->banos                 = Input::get('banos');
					$t2->agua_caliente         = Input::get('agua_caliente');            
					$t2->estacionamientos      = Input::get('estacionamientos');
					$t2->cuota_mant            = Input::get('cuota_mant');			
					$t2->recargo               = Input::get('recargo');
					$t2->descuento             = Input::get('descuento');	
					$t2->d_registra_cmpc       = Input::get('d_registra_cmpc');
					$t2->d_vence             	 = Input::get('d_vence');
					$t2->m_descuento           = Input::get('m_descuento');
					$t2->area                  = Input::get('area');
					
					$t2->f_iniciaextra         = Carbon::parse(Input::get('f_iniciaextra'))->startOfMonth();
					$t2->extra_meses           = Input::get('extra_meses');
					$t2->extra                 = Input::get('extra');

					$t2->seccione_id           = $dato->id;
					$t2->save();
	
				}	elseif ($dato->tipo == 3) { 
					// Salva en la tabla seclceds "Local comercial en condominios"
					$t3 = new Seclced;
					$t3->banos                  = Input::get('banos');
					$t3->agua_caliente          = Input::get('agua_caliente');            
					$t3->estacionamientos       = Input::get('estacionamientos');
					$t3->cuota_mant             = Input::get('cuota_mant');
					$t3->recargo                = Input::get('recargo');
					$t3->descuento              = Input::get('descuento');	
					$t3->d_registra_cmpc        = Input::get('d_registra_cmpc');
					$t3->d_vence              	= Input::get('d_vence');
					$t3->m_descuento            = Input::get('m_descuento');
					$t3->area                   = Input::get('area');
					$t3->seccione_id            = $dato->id;
					$t3->save();
	
				}	elseif ($dato->tipo == 4) { 
					// Salva en la tabla seclcres "Local comercial en residencial"
 					$t4 = new Seclcre;
					$t4->avenida                = Input::get('avenida');
					$t4->banos                  = Input::get('banos');
					$t4->agua_caliente          = Input::get('agua_caliente');            
					$t4->estacionamientos       = Input::get('estacionamientos');
					$t4->cuota_mant             = Input::get('cuota_mant');
					$t4->recargo                = Input::get('recargo');
					$t4->descuento              = Input::get('descuento');	
					$t4->d_registra_cmpc        = Input::get('d_registra_cmpc');
					$t4->d_vence              	= Input::get('d_vence');
					$t4->m_descuento            = Input::get('m_descuento');
					$t4->area                   = Input::get('area');
					$t4->seccione_id            = $dato->id;
					$t4->save();
				
				}			

				// Actualiza la ruta de la imagen de la Sección
				$img_path = Seccione::find($dato->id);
				$img_path->imagen_L = "assets/img/secciones/sec_L".$dato->id.".jpg";
				$img_path->save();		

  			Sity::RegistrarEnBitacora($dato, Input::get(), 'Seccione', 'Crea nueva seccion');
				DB::commit();
				
				Session::flash('success', 'La Sección administrativa ' .$dato->nombre. ' ha sido creada con éxito.');
				return redirect()->route('indexsecplus', Input::get('bloque_id'));
			}
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en SeccionesController.store, la transaccion ha sido cancelada!');
			return back()->withInput();
		}

	}

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario para edición
	 ************************************************************************************/	
	public function edit($seccione_id)
	{
		//Obtiene datos de la Seccion administrativa que se desea editar.
		//Encuentra la Sección no importa el tipo
		$sec = Seccione::find($seccione_id);
		//dd($sec->toArray());
		
		//Almacena una lista de todos los Phs para ser enviados al view.
		$phs = Ph::pluck('nombre', 'id')->all();  		

		if ($sec->tipo == 1) {
				//$dato = Seccione::find($id)->secapto; // trae solamente los datos de secaptos
			$dato = Seccione::with('secapto')->find($seccione_id); // trae los datos de las dos tablas
		
		} elseif ($sec->tipo == 2) {
				$dato = Seccione::with('secre')->find($seccione_id);
		
		} elseif ($sec->tipo == 3) {
				$dato = Seccione::with('seclced')->find($seccione_id);
		
		} elseif ($sec->tipo == 4) {
				$dato = Seccione::with('seclcre')->find($seccione_id);
		
		}
		//dd($dato->toArray());		
		
		return view('core.secciones.edit')
									->with('dato', $dato)
									->with('phs', $phs);
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
				'nombre'    			=> 'required',
				'descripcion' 		=> 'required',
				'ph_id'						=> 'required',
				'd_registra_cmpc' => 'required'
			);

			$messages = [
				'required' => 'El campo :attribute es requerido!',
				'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
			];        
					
			$validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
		
				// Actualiza los datos que corresponden a la tabla Secciones
				$dato = Seccione::find($id);
				$dato->nombre       	 = Input::get('nombre');
				$dato->descripcion     = Input::get('descripcion');
				$dato->ph_id           = Input::get('ph_id');			
				$dato->save();
				
				// Relaciones de uno a uno con Secciones
				// Actualiza en la tabla Secapto
				if ($dato->tipo == 1) {  
					$t1 = Seccione::find($id)->Secapto;
					$t1->cuartos               = Input::get('cuartos');
					$t1->banos                 = Input::get('banos');
					$t1->agua_caliente         = Input::get('agua_caliente');            
					$t1->estacionamientos      = Input::get('estacionamientos');
					$t1->cuota_mant            = Input::get('cuota_mant');
					$t1->recargo               = Input::get('recargo');
					$t1->descuento             = Input::get('descuento');	
					$t1->d_registra_cmpc       = Input::get('d_registra_cmpc');
					$t1->d_vence       	   	   = Input::get('d_vence');
					$t1->m_vence       	   	   = Input::get('m_vence');
					$t1->m_descuento       	   = Input::get('m_descuento');
					$t1->f_iniciaextra         = Carbon::parse(Input::get('f_iniciaextra'))->startOfMonth();
					$t1->extra_meses           = Input::get('extra_meses');
					$t1->extra                 = Input::get('extra');
					$t1->area                  = Input::get('area');
					$t1->save();			
				}

				// Actualiza en la tabla Secres
				elseif ($dato->tipo == 2) {  
					$t2 = Seccione::find($id)->Secre;
					$t2->avenida               = Input::get('avenida');
					$t2->cuartos               = Input::get('cuartos');
					$t2->banos                 = Input::get('banos');
					$t2->agua_caliente         = Input::get('agua_caliente');            
					$t2->estacionamientos      = Input::get('estacionamientos');
					$t2->cuota_mant            = Input::get('cuota_mant');			
					$t2->recargo               = Input::get('recargo');
					$t2->descuento             = Input::get('descuento');	
					$t2->d_registra_cmpc       = Input::get('d_registra_cmpc');
					$t2->d_vence       	   	   = Input::get('d_vence');
					$t2->m_vence       	   	   = Input::get('m_vence');
					$t2->m_descuento       	   = Input::get('m_descuento');
					$t2->area                  = Input::get('area');
					$t2->save();
				}

				// Actualiza en la tabla Seclceds
				elseif ($dato->tipo == 3) {  
					$t3 = Seccione::find($id)->Seclced;
					$t3->banos                  = Input::get('banos');
					$t3->agua_caliente          = Input::get('agua_caliente');            
					$t3->estacionamientos       = Input::get('estacionamientos');
					$t3->cuota_mant             = Input::get('cuota_mant');
					$t3->recargo                = Input::get('recargo');
					$t3->descuento              = Input::get('descuento');	
					$t3->d_registra_cmpc        = Input::get('d_registra_cmpc');
					$t3->d_vence       	   	    = Input::get('d_vence');
					$t3->m_vence       	   	    = Input::get('m_vence');
					$t3->m_descuento       	    = Input::get('m_descuento');
					$t3->area                   = Input::get('area');
					$t3->save();
				}

				// Actualiza en la tabla Seclcres
				elseif ($dato->tipo == 4) {  
					$t4 = Seccione::find($id)->Seclcre;
					$t4->avenida                = Input::get('avenida');
					$t4->banos                  = Input::get('banos');
					$t4->agua_caliente          = Input::get('agua_caliente');            
					$t4->estacionamientos       = Input::get('estacionamientos');
					$t4->cuota_mant             = Input::get('cuota_mant');
					$t4->recargo                = Input::get('recargo');
					$t4->descuento              = Input::get('descuento');	
					$t4->d_registra_cmpc        = Input::get('d_registra_cmpc');
					$t4->d_vence       	   	    = Input::get('d_vence');
					$t4->m_vence       	   	    = Input::get('m_vence');
					$t4->m_descuento       	    = Input::get('m_descuento');
					$t4->area                   = Input::get('area');
					$t4->save();
				}	

  			Sity::RegistrarEnBitacora($dato, Input::get(), 'Seccione', 'Edita seccion');
  			DB::commit();				
				
				Session::flash('success', 'La Sección administrativa ' . $dato->nombre . ' ha sido editada con éxito.');
				return redirect()->route('indexsecplus', Input::get('bloque_id'));
			}
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en SeccionesController.update, la transaccion ha sido cancelada!');
			return back()->withInput();
		}  	
	}

	/*************************************************************************************
	 * Borra registro de la base de datos
	 ************************************************************************************/	
	public function destroy($seccione_id)
	{
		
		DB::beginTransaction();
		try {
			//dd($seccione_id);
			/* No se permitirá borrar aquellos Secciones administrativas que cumplan con por lo menos una de siguientes condiciones:
				1. Secciones que tengan Unidades asignadas. */
			
			$dato = Seccione::find($seccione_id);
			
			// Revisa si hay alguna unidad asignada a la junta directiva
			$un= Un::where('seccione_id', $seccione_id)->first();
			//dd($un);

			if(!empty($un)) {
				Session::flash('success', 'La Sección ' .$dato->nombre. ' no puede ser borrada porque tiene uno o más unidades asignadoa a la misma.');
			}
			
			else {
			
				if ($dato->tipo == 1) {  		
					$d = Secapto::where('seccione_id', $dato->id)->first();
					$d->delete();
				}
				
				elseif ($dato->tipo == 2) {  		
					$d = Secre::where('seccione_id', $dato->id)->first();
					$d->delete();
				}		
				
				elseif ($dato->tipo == 3) {  		
					$d = Seclced::where('seccione_id', $dato->id)->first();
					$d->delete();
				}
				
				elseif ($dato->tipo == 4) {  		
					$d = Seclcre::where('seccione_id', $dato->id)->first();
					$d->delete();
				}		
				
				$dato->delete();

				// Registra en bitacoras
  			Sity::RegistrarEnBitacora($bloque, Null, 'Seccione', 'Elimina seccion'); 
				DB::commit();
				
				Session::flash('success', 'La Sección administrativa ' .$dato->nombre. ' ha sido borrada permanentemente de la base de datos.');		
			}
			return back();
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en penalizar.php, la transaccion ha sido cancelada!');
			return back();
		}
	}

  /*************************************************************************************
	 * Sube una imagen a la carpeta de bloques
	 ************************************************************************************/	
	public function subirImagenSeccion($id)
	{
	  //DB::beginTransaction();
	  //try {
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
				$destinationPath = "assets/img/secciones";
				$filename = "sec-L".$id.".jpg";

				$uploadSuccess = Input::file('file')->move($destinationPath, $filename);
				if( $uploadSuccess ) {
					// Actualiza la ruta de la imagen del bloque
					$img_path = Seccione::find($id);
					$img_path->imagen_L = "assets/img/secciones/sec-L".$id.".jpg";
					$img_path->imagen_M = "assets/img/secciones/sec-M".$id.".jpg";
					$img_path->imagen_S = "assets/img/secciones/sec-S".$id.".jpg";
  				//Sity::RegistrarEnBitacora($img_path, $input, 'Bloque', 'Actualiza imagen de bloque');	
					$img_path->save();
					
					// crea imagen normal
					// resize the image to a height of 300 and constrain aspect ratio (auto width)
					$img = Image::make($img_path->imagen_L)->resize(900, 500);
					$img->save("assets/img/secciones/sec-L".$id.".jpg");
					
					// crea thumpnail No 1
					// resize the image to a height of 189 and constrain aspect ratio (auto width)
					$img = Image::make($img_path->imagen_L)->resize(189, 189);
					$img->save("assets/img/secciones/sec-M".$id.".jpg");

					// crea thumpnail No 2 
					// resize the image to a height of 90 and constrain aspect ratio (auto width)
					$img = Image::make($img_path->imagen_L)->resize(90, 90);
					$img->save("assets/img/secciones/sec-S".$id.".jpg");			
  			
	  			DB::commit();
					
					Session::flash('success', 'La imagen se actualizó con éxito.');
					return back()->withInput();
				
				} else {
					Session::flash('danger', 'La imagen no se pudo subir.');
					return back()->withInput();
				}

	  //} catch (\Exception $e) {
	    //DB::rollback();
	    //Session::flash('warning', ' Ocurrio un error en SeccionesController.subirImagenSeccione, la transaccion ha sido cancelada!');
	    //return back()->withInput();
	  //}
	}

}