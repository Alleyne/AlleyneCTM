<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Redirect, Session;
use Grupo;
use Validator;
use Image;

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
		$blqadmins=blqadmin::where('bloque_id', $bloque_id)
						   ->where('encargado','1')
						   ->count();
 		//dd($blqadmins);
 		
 		//verifica que el bloque tenga por lo menos un administrador encargado
 		if ($blqadmins==0) {
			Session::flash('warning', 'Es obligatorio que cada bloque tenga por lo menos un administrador encargado o responsable del mismo. Favor vincular por lo menos un adminstrador ecargado o responsable!');
			return Redirect::route('indexblqadmin', $bloque_id);
 		}
 		elseif ($blqadmins>1) {
			Session::flash('warning', 'Un bloque puede tener uno o muchos administradores pero solamente uno puede ser encargado o responsable del mismo. Este bloque tiene mas de uno!.');
			return Redirect::route('indexblqadmin', $bloque_id);
 		}
	    

	    //Obtiene los datos del la Justa Directiva	    
	    $jd = Jd::first();

	    //Obtiene los datos del Bloques    
	    $bloque = Bloque::find($bloque_id);
	    //dd($bloque->toArray());
	    
	    //Obtiene los datos del o los administradores del bloque
		$blqadmins=blqadmin::where('bloque_id', $bloque->id)
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
	    
	    return view('backend.secciones.indexsecplus')
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
        	return Redirect::back();
		}
		
		if ($sec->tipo==1) {
		 	$seccion = Seccione::with('secapto')->find($seccione_id); // trae los datos de las dos tablas
		}
		elseif ($sec->tipo==2) {
		    $seccion = Seccione::with('secre')->find($seccione_id);
		}
		elseif ($sec->tipo==3) {
		    $seccion = Seccione::with('seclced')->find($seccione_id);
		}
		elseif ($sec->tipo==4) {
		    $seccion = Seccione::with('seclcre')->find($seccione_id);
		}
		elseif ($sec->tipo==5 or $sec->tipo==6 or $sec->tipo==7) {
		    $seccion = $sec;
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
		
	    return view('backend.secciones.showsecplus')
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
        return view('backend.secciones.createsec')
        			->with('bloque_id', $bloque_id)
        			->with('tipo', $tipo);
	}

   /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        $input = Input::all();
        //dd($input);
 
 	    $rules = array(
            'nombre'    	=> 'required',
            'descripcion' 	=> 'required',
          	'codigo'    	=> 'Required|Min:2|Max:2|Alpha_num',
            'd_registra_cmpc' 	=> 'required'	    
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
			$dato->nombre       	 = Input::get('nombre');
			$dato->codigo       	 = Input::get('codigo');
			$dato->descripcion       = Input::get('descripcion');
			$dato->bloque_id         = Input::get('bloque_id');			
			$dato->save();
			
			// Relaciones de uno a uno con Secciones
			// Salva en la tabla Sec-apto
			if ($dato->tipo==1) {  
				$t1 = new Secapto;
				$t1->cuartos               = Input::get('cuartos');
				$t1->banos                 = Input::get('banos');
				$t1->agua_caliente         = Input::get('agua_caliente');            
				$t1->estacionamientos      = Input::get('estacionamientos');
				$t1->cuota_mant            = Input::get('cuota_mant');
				$t1->recargo               = Input::get('recargo');
				$t1->descuento             = Input::get('descuento');								
				$t1->d_registra_cmpc       = Input::get('d_registra_cmpc');
				$t1->d_gracias             = Input::get('d_gracias');
				$t1->m_descuento           = Input::get('m_descuento');
				$t1->area                  = Input::get('area');
				$t1->seccione_id           = $dato->id;
				$t1->save();			
				
				// Registra en bitacoras
				$detalle =	'Crea la Sección '.   $dato->nombre. ', '.
							'codigo= '.   		  $dato->codigo. ', '.
							'tipo= '.		   	  'Apartamentos, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'cuartos= '.   		  $t1->cuartos. ', '.
							'banos= '.   		  $t1->banos. ', '.
							'agua_caliente= '.    $t1->agua_caliente. ', '.
							'estacionamientos= '. $t1->estacionamientos. ', '.
							'cuota_mant= '.   	  $t1->cuota_mant. ', '.
							'recargo= '.   	  	  $t1->recargo. ', '.
							'descuento= '.   	  $t1->descuento. ', '.
							'd_registra_cmpc= '.  $t1->d_registra_cmpc. ', '.
							'd_gracias= '.   	  $t1->d_gracias. ', '.
							'm_descuento= '.   	  $t1->m_descuento. ', '.
							'area= '.   	      $t1->area. ', '. 
							'seccione_id= '.   	  $t1->seccione_id;			
			}

			// Salva en la tabla Sec-res
			elseif ($dato->tipo==2) {  
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
				$t2->d_gracias             = Input::get('d_gracias');
				$t2->m_descuento           = Input::get('m_descuento');
				$t2->area                  = Input::get('area');
				$t2->seccione_id           = $dato->id;
				$t2->save();
			
				// Registra en bitacoras
				$detalle =	'Crea la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Residencias, '.
							'codigo= '.   		  $dato->codigo. ', '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'avenida= '.   		  $t2->avenida. ', '.
							'cuartos= '.   		  $t2->cuartos. ', '.
							'banos= '.   		  $t2->banos. ', '.
							'agua_caliente= '.    $t2->agua_caliente. ', '.
							'estacionamientos= '. $t2->estacionamientos. ', '.
							'recargo= '.   	  	  $t2->recargo. ', '.
							'descuento= '.   	  $t2->descuento. ', '.
							'cuota_mant= '.   	  $t2->cuota_mant. ', '.
							'd_registra_cmpc= '.  $t2->d_registra_cmpc. ', '.
							'd_gracias= '.   	  $t2->d_gracias. ', '.
							'm_descuento= '.   	  $t2->m_descuento. ', '.
							'area= '.   	      $t2->area. ', '. 
							'seccione_id= '.   	  $t2->seccione_id;			
			}

			// Salva en la tabla Seclceds
			elseif ($dato->tipo==3) {  
				$t3 = new Seclced;
				$t3->banos                  = Input::get('banos');
				$t3->agua_caliente          = Input::get('agua_caliente');            
				$t3->estacionamientos       = Input::get('estacionamientos');
				$t3->cuota_mant             = Input::get('cuota_mant');
				$t3->recargo                = Input::get('recargo');
				$t3->descuento              = Input::get('descuento');	
				$t3->d_registra_cmpc        = Input::get('d_registra_cmpc');
				$t3->d_gracias              = Input::get('d_gracias');
				$t3->m_descuento            = Input::get('m_descuento');
				$t3->area                   = Input::get('area');
				$t3->seccione_id            = $dato->id;
				$t3->save();
				
				// Registra en bitacoras
				$detalle =	'Crea la Sección '.   $dato->nombre. ', '.
							'codigo= '.   		  $dato->codigo. ', '.
							'tipo= '.		   	  'Local u oficinas en edificio, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'banos= '.   		  $t3->banos. ', '.
							'agua_caliente= '.    $t3->agua_caliente. ', '.
							'estacionamientos= '. $t3->estacionamientos. ', '.
							'cuota_mant= '.   	  $t3->cuota_mant. ', '.
							'recargo= '.   	  	  $t3->recargo. ', '.
							'descuento= '.   	  $t3->descuento. ', '.
							'd_registra_cmpc= '.  $t3->d_registra_cmpc. ', '.
							'd_gracias= '.   	  $t3->d_gracias. ', '.
							'm_descuento= '.   	  $t3->m_descuento. ', '.
							'area= '.   	      $t3->area. ', '. 
							'seccione_id= '.   	  $t3->seccione_id;			
			}

			// Salva en la tabla Seclcres
			elseif ($dato->tipo==4) {  
				$t4 = new Seclcre;
				$t4->avenida                = Input::get('avenida');
				$t4->banos                  = Input::get('banos');
				$t4->agua_caliente          = Input::get('agua_caliente');            
				$t4->estacionamientos       = Input::get('estacionamientos');
				$t4->cuota_mant             = Input::get('cuota_mant');
				$t4->recargo                = Input::get('recargo');
				$t4->descuento              = Input::get('descuento');	
				$t4->d_registra_cmpc        = Input::get('d_registra_cmpc');
				$t4->d_gracias              = Input::get('d_gracias');
				$t4->m_descuento            = Input::get('m_descuento');
				$t4->area                   = Input::get('area');
				$t4->seccione_id            = $dato->id;
				$t4->save();
			
				// Registra en bitacoras
				$detalle =	'Crea la Sección '.   $dato->nombre. ', '.
							'codigo= '.   		  $dato->codigo. ', '.
							'tipo= '.		   	  'Locales u oficinas en residencial, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'avenida= '.   		  $t4->avenida. ', '.
							'banos= '.   		  $t4->banos. ', '.
							'agua_caliente= '.    $t4->agua_caliente. ', '.
							'estacionamientos= '. $t4->estacionamientos. ', '.
							'cuota_mant= '.   	  $t4->cuota_mant. ', '.
							'recargo= '.   	  	  $t4->recargo. ', '.
							'descuento= '.   	  $t4->descuento. ', '.
							'd_registra_cmpc= '.  $t4->d_registra_cmpc. ', '.
							'd_gracias= '.   	  $t4->d_gracias. ', '.
							'm_descuento= '.   	  $t4->m_descuento. ', '.
							'area= '.   	      $t4->area. ', '. 
							'seccione_id= '.   	  $t4->seccione_id;			
			}

			// Salva en la tabla ams
			elseif ($dato->tipo==5) {  

				// Registra en bitacoras
				$detalle =	'Crea la Sección '.   $dato->nombre. ', '.
							'codigo= '.   		  $dato->codigo. ', '.
							'tipo= '.		   	  'Amenidades propias, '.
							'descripcion= '.   	  $dato->descripcion; 
			}			

			// Actualiza la ruta de la imagen de la Sección
			$img_path = Seccione::find($dato->id);
			$img_path->imagen_L = "assets/img/secciones/sec_L".$dato->id.".jpg";
			$img_path->save();		

			Sity::RegistrarEnBitacora(1, 'secciones', $dato->id, $detalle);
			Session::flash('success', 'La Sección administrativa ' .$dato->nombre. ' ha sido creada con éxito.');
			return Redirect::route('indexsecplus', Input::get('bloque_id'));
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     ************************************************************************************/	
	public function edit($seccione_id)
	{
		//Obtiene datos de la Seccion administrativa que se desea editar.
		//Encuentra la Sección no importa el tipo
		$sec=Seccione::find($seccione_id);
		//dd($sec->toArray());
		
		//Almacena una lista de todos los Phs para ser enviados al view.
        $phs = Ph::lists('nombre', 'id')->toArray();  		

		if ($sec->tipo==1) {
		    //$dato = Seccione::find($id)->secapto; // trae solamente los datos de secaptos
		 	$dato = Seccione::with('secapto')->find($seccione_id); // trae los datos de las dos tablas
		}
		elseif ($sec->tipo==2) {
		    $dato = Seccione::with('secre')->find($seccione_id);
		}
		elseif ($sec->tipo==3) {
		    $dato = Seccione::with('seclced')->find($seccione_id);
		}
		elseif ($sec->tipo==4) {
		    $dato = Seccione::with('seclcre')->find($seccione_id);
		}
		elseif ($sec->tipo==5 or $sec->tipo==6 or $sec->tipo==7) {
		    $dato = $sec;
		}
		//dd($dato->toArray());		
		
    	return view('backend.secciones.edit')
                    ->with('dato', $dato)
                    ->with('phs', $phs);
	}

    /*************************************************************************************
     * Actualiza registro
     ************************************************************************************/
	public function update($id)
	{
        //dd(Input::get());
        $input = Input::all();
        $rules = array(
            'nombre'    	=> 'required',
            'descripcion' 	=> 'required',
            'd_registra_cmpc' 	=> 'required'
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
			$dato->descripcion       = Input::get('descripcion');
			$dato->ph_id             = Input::get('ph_id');			
			$dato->save();
			
			// Relaciones de uno a uno con Secciones
			// Actualiza en la tabla Secapto
			if ($dato->tipo==1) {  
				$t1 = Seccione::find($id)->Secapto;
				$t1->cuartos               = Input::get('cuartos');
				$t1->banos                 = Input::get('banos');
				$t1->agua_caliente         = Input::get('agua_caliente');            
				$t1->estacionamientos      = Input::get('estacionamientos');
				$t1->cuota_mant            = Input::get('cuota_mant');
				$t1->recargo               = Input::get('recargo');
				$t1->descuento             = Input::get('descuento');	
				$t1->d_registra_cmpc       = Input::get('d_registra_cmpc');
				$t1->d_gracias       	   = Input::get('d_gracias');
				$t1->m_descuento       	   = Input::get('m_descuento');
				$t1->area                  = Input::get('area');
				$t1->save();			
				
				// Registra en bitacoras
				$detalle =	'Edita la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Apartamentos, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'ph_id= '.   		  $dato->ph_id. ', '.
							'cuartos= '.   		  $t1->cuartos. ', '.
							'banos= '.   		  $t1->banos. ', '.
							'agua_caliente= '.    $t1->agua_caliente. ', '.
							'estacionamientos= '. $t1->estacionamientos. ', '.
							'cuota_mant= '.   	  $t1->cuota_mant. ', '.
							'recargo= '.   	  	  $t1->recargo. ', '.
							'descuento= '.   	  $t1->descuento. ', '.
							'd_registra_cmpc= '.  $t1->d_registra_cmpc. ', '.
							'd_gracias= '.  	  $t1->d_gracias. ', '.
							'm_descuento= '.  	  $t1->m_descuento. ', '.
							'area= '.   	      $t1->area;
			}

			// Actualiza en la tabla Secres
			elseif ($dato->tipo==2) {  
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
				$t2->d_gracias       	   = Input::get('d_gracias');
				$t2->m_descuento       	   = Input::get('m_descuento');
				$t2->area                  = Input::get('area');
				$t2->save();
			
				// Registra en bitacoras
				$detalle =	'Edita la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Residencias, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'avenida= '.   		  $t2->avenida. ', '.
							'cuartos= '.   		  $t2->cuartos. ', '.
							'banos= '.   		  $t2->banos. ', '.
							'agua_caliente= '.    $t2->agua_caliente. ', '.
							'estacionamientos= '. $t2->estacionamientos. ', '.
							'cuota_mant= '.   	  $t2->cuota_mant. ', '.
							'recargo= '.   	  	  $t2->recargo. ', '.
							'descuento= '.   	  $t2->descuento. ', '.
							'd_registra_cmpc= '.  $t2->d_registra_cmpc. ', '.
							'd_gracias= '.  	  $t2->d_gracias. ', '.
							'm_descuento= '.  	  $t1->m_descuento. ', '.
							'area= '.   	      $t2->area;  
			}

			// Actualiza en la tabla Seclceds
			elseif ($dato->tipo==3) {  
				$t3 = Seccione::find($id)->Seclced;
				$t3->banos                  = Input::get('banos');
				$t3->agua_caliente          = Input::get('agua_caliente');            
				$t3->estacionamientos       = Input::get('estacionamientos');
				$t3->cuota_mant             = Input::get('cuota_mant');
				$t3->recargo                = Input::get('recargo');
				$t3->descuento              = Input::get('descuento');	
				$t3->d_registra_cmpc        = Input::get('d_registra_cmpc');
				$t3->d_gracias       	    = Input::get('d_gracias');
				$t3->m_descuento       	    = Input::get('m_descuento');
				$t3->area                   = Input::get('area');
				$t3->save();
			
				// Registra en bitacoras
				$detalle =	'Edita la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Local u oficinas en edificio, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'banos= '.   		  $t3->banos. ', '.
							'agua_caliente= '.    $t3->agua_caliente. ', '.
							'estacionamientos= '. $t3->estacionamientos. ', '.
							'cuota_mant= '.   	  $t3->cuota_mant. ', '.
							'recargo= '.   	  	  $t3->recargo. ', '.
							'descuento= '.   	  $t3->descuento. ', '.
							'd_registra_cmpc= '.  $t3->d_registra_cmpc. ', '.
							'd_gracias= '.  	  $t3->d_gracias. ', '.
							'm_descuento= '.  	  $t1->m_descuento. ', '.
							'area= '.   	      $t3->area; 
			}

			// Actualiza en la tabla Seclcres
			elseif ($dato->tipo==4) {  
				$t4 = Seccione::find($id)->Seclcre;
				$t4->avenida                = Input::get('avenida');
				$t4->banos                  = Input::get('banos');
				$t4->agua_caliente          = Input::get('agua_caliente');            
				$t4->estacionamientos       = Input::get('estacionamientos');
				$t4->cuota_mant             = Input::get('cuota_mant');
				$t4->recargo                = Input::get('recargo');
				$t4->descuento              = Input::get('descuento');	
				$t4->d_registra_cmpc        = Input::get('d_registra_cmpc');
				$t4->d_gracias       	    = Input::get('d_gracias');
				$t4->m_descuento       	    = Input::get('m_descuento');
				$t4->area                   = Input::get('area');
				$t4->save();
			
				// Registra en bitacoras
				$detalle =	'Edita la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Locales u oficinas en residencial, '.
							'descripcion= '.   	  $dato->descripcion. ', '.
							'avenida= '.   		  $t4->avenida. ', '.
							'banos= '.   		  $t4->banos. ', '.
							'agua_caliente= '.    $t4->agua_caliente. ', '.
							'estacionamientos= '. $t4->estacionamientos. ', '.
							'cuota_mant= '.   	  $t4->cuota_mant. ', '.
							'recargo= '.   	  	  $t4>recargo. ', '.
							'descuento= '.   	  $t4->descuento. ', '.
							'd_registra_cmpc= '.  $t4->d_registra_cmpc. ', '.
							'd_gracias= '.  	  $t4->d_gracias. ', '.
							'm_descuento= '.  	  $t1->m_descuento. ', '.
							'area= '.   	      $t4->area; 
			}			

			elseif ($dato->tipo==5) {  
			
				// Registra en bitacoras
				$detalle =	'Edita la Sección '.   $dato->nombre. ', '.
							'tipo= '.		   	  'Amenidades propias, '.
							'descripcion= '.   	  $dato->descripcion; 
			}

			Sity::RegistrarEnBitacora(2, 'secciones', $dato->id, $detalle);
			Session::flash('success', 'La Sección administrativa ' . $dato->nombre . ' ha sido editada con éxito.');
			return Redirect::route('indexsecplus', Input::get('bloque_id'));
		}
        return Redirect::back()->withInput()->withErrors($validation);
  	}

    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($seccione_id)
	{
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
		
			if ($dato->tipo==1) {  		
				$d = Secapto::where('seccione_id', $dato->id)->first();
				$d->delete();
				
				$detalle =	'Borra la Sección '. $dato->nombre. ', tipo apartamento '. 
							'con la siguiente descripcion: '.  $dato->descripcion;
			}
			
			elseif ($dato->tipo==2) {  		
				$d = Secre::where('seccione_id', $dato->id)->first();
				$d->delete();
				
				$detalle =	'Borra la Sección '. $dato->nombre. ', tipo residencia '.
							'con la siguiente descripcion: '.  $dato->descripcion;
			}		
			
			elseif ($dato->tipo==3) {  		
				$d = Seclced::where('seccione_id', $dato->id)->first();
				$d->delete();
				
				$detalle =	'Borra la Sección '. $dato->nombre. ', oficina o local comercial en edificio '.
							'con la siguiente descripcion: '.  $dato->descripcion;
			}
			
			elseif ($dato->tipo==4) {  		
				$d = Seclcre::where('seccione_id', $dato->id)->first();
				$d->delete();
				
				$detalle =	'Borra la Sección '. $dato->nombre. ', oficina o local comercial en residencial'.
							'con la siguiente descripcion: '.  $dato->descripcion;
			}		
			
			$dato->delete();

			// Registra en bitacoras
			Sity::RegistrarEnBitacora(3, 'secciones', $dato->id, $detalle);		
			Session::flash('success', 'La Sección administrativa ' .$dato->nombre. ' ha sido borrada permanentemente de la base de datos.');		
		}
	
		return Redirect::back();
	}
}