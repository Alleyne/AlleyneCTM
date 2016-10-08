<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Redirect, Session;
use Grupo;
use Validator;
use Image;
use Debugbar;
use URL;
use Cache;

use App\Jd;
use App\Bloque;
use App\Seccione;
use App\Un;
use App\Secre;
use App\Secapto;
use App\Seclced;
use App\Seclcre;
use App\User;
use App\Bitacora;
use App\Ctdasm;
use App\Blqadmin;
use App\Prop;

class UnsController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }

    /*************************************************************************************
     * Despliega todas las unidades
     ************************************************************************************/	
	public function indexunall()
	{

	    // Almacena los datos de las unidades
	    $datos = Cache::get('unsAllkey');
	    //dd($datos->toArray());
	    		
		// Determina el estatus de la unidad (Paz y salvo o Moroso)
		$i=0;		
		
		foreach ($datos as $dato) {
			$estatus1 = Ctdasm::where('un_id', $dato->id)
							 ->where('pagada', 0)
							 ->first();

			$estatus2 = Ctdasm::where('un_id', $dato->id)
							 ->Where('recargo_siono', 1)
							 ->Where('recargo_pagado', 0)							 
							 ->first();
			
		    if(!is_null($estatus1) || !is_null($estatus2)) {
		    	$datos[$i]['estatus']='Moroso';
			}
		    elseif(is_null($estatus1) && is_null($estatus2)) {
		    	$datos[$i]['estatus']='Paz y salvo';
		    }
    
		    $props=Prop::where('un_id', $dato->id)
		    		   ->join('users','users.id','=','props.user_id')
		    		   ->select('cedula','nombre_completo')
		    		   ->get();
			//dd($props->toArray());			

			$propietarios="";
			foreach ($props as $prop) {
				if ($propietarios=="") {
					$propietarios=$prop->cedula.' '.$prop->nombre_completo;
				}
				else {
					$propietarios=$propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;					
				}
			}
			//dd($propietarios);		    
		    $datos[$i]['propietarios']=$propietarios;
		    $i++;
		}       
        //dd($propietarios);
		
		Cache::forever('indexunallkey', URL::full());
		
		//dd($datos->toArray());        
        return view('backend.uns.indexunall')
                ->with('datos', $datos);
	}
     
    /*************************************************************************************
     * Despliega todas las unidades que pertenecen a una determinada Sección.
     ************************************************************************************/	
	public function indexunplus($seccione_id)
	{
	    //dd($seccione_id);
	    //Obtiene todas las Secciones administrativas que pertenecen a un determinado Bloque
	    //no importa el tipo de sección.
	    $seccion = Seccione::with('ph')->find($seccione_id);
	    //dd($secciones->toArray());        

		if (is_null($seccion->ph)) {
			Session::flash('warning', 'Es necesario asignar un Ph a cada Sección, favor utilizar el botón Editar para asignarle un Ph a esta Sección!');
        	return Redirect::back();
		}	    

	    //Obtiene los datos del Bloques 
	    $bloque = Bloque::find($seccion->bloque_id);
	    //dd($bloques->toArray());        
	    
 	    //Obtiene los datos de la Junta Directiva
	    $jd = Jd::find($bloque->jd_id);		
	    //dd($jd->toArray());      
	    
	    //Obtiene los datos del o los adiministradores del bloque
		$blqadmins=blqadmin::where('bloque_id', $bloque->id)
	                    				->with('user')
	                    				->with('org')
	                    				->get();
	    //dd($blqadmins->toArray());
	    
	    // Almacena los datos de las unidades que pertenecen a una determinada sección
	    $datos = Un::where('seccione_id', $seccione_id)
	               ->orderBy('id', 'ASC')
	               ->select('id', 'codigo', 'inicializada', 'activa')
	               ->get();
	    //dd($datos->toArray());
	    
		// Determina el estatus de la unidad (Paz y salvo o Moroso)
		$i=0;		
		foreach ($datos as $dato) {
			$estatus = Ctdasm::where('un_id', $dato->id)
							 ->where('pagada', '0')
							 ->first();
		    
		    if(!empty($estatus)) {
		    	$datos[$i]['estatus']='Moroso';
			}
		    else {
		    	$datos[$i]['estatus']='Paz y salvo';
		    }
		    
		    // recorta el codigo de la unidad
		    //$dato->codigo=strtok($dato->codigo, '+');
		    
		    $props=Prop::where('un_id', $dato->id)
		    		   ->join('users','users.id','=','props.user_id')
		    		   ->select('cedula','nombre_completo')
		    		   ->get();
			//dd($props->toArray());			

			$propietarios="";
			foreach ($props as $prop) {
				if ($propietarios=="") {
					$propietarios=$prop->cedula.' '.$prop->nombre_completo;
				}
				else {
					$propietarios=$propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;					
				}
			}
			//dd($propietarios);		    
		    $datos[$i]['propietarios']=$propietarios;
		    $i++;
		}       
        //dd($propietarios);
		
		Cache::forever('indexunallkey', URL::full());
        
        return view('backend.uns.indexunplus')
                ->with('jd', $jd)
                ->with('datos', $datos)        
                ->with('seccion', $seccion) 
                ->with('bloque', $bloque)
                ->with('blqadmins', $blqadmins);
	}	       

    /***********************************************************************************
     * Despliega el registro especificado en formato formulario sólo lectura
    ************************************************************************************/	
	public function show($un_id)
	{
	    $dato = Un::find($un_id);
	    //dd($dato->toArray());
	    
	    if(!empty($dato)) {
			//obtiene los datos de la sección
			$seccion = Seccione::find($dato->seccione_id);	
			//dd($seccion->toArray());			
			
			// encuentra a los propietarios
			$props=Prop::where('un_id', $un_id)
						->with('user')
						->get();
			

			if ($seccion->tipo==1) {
				$secapto=Secapto::where('seccione_id', $dato->seccione_id)->first();
				//dd($secapto->toArray());
			
				return view('backend.uns.show')
				            ->with('dato', $dato)
				            ->with('secapto', $secapto)
				            ->with('props', $props)
				            ->with('seccion', $seccion);
			}
			
			elseif ($seccion->tipo==2) {
				$secre=Secre::where('seccione_id', $dato->seccione_id)->first();
				//dd($secre->toArray());
			
				return view('backend.uns.show')
				            ->with('dato', $dato)
				            ->with('secre', $secre)
				            ->with('seccion', $seccion);
			}

			elseif ($seccion->tipo==3) {
				$seclced=Seclced::where('seccione_id', $dato->seccione_id)->first();
				//dd($Seccled->toArray());
			
				return view('backend.uns.show')
				            ->with('dato', $dato)
				            ->with('seclced', $seclced)
				            ->with('seccion', $seccion);
			}

			elseif ($seccion->tipo==4) {
				$seclcre=Seclcre::where('seccione_id', $dato->seccione_id)->first();
				//dd($Secclre->toArray());
			
				return view('backend.uns.show')
				            ->with('dato', $dato)
				            ->with('seclcre', $seclcre)
				            ->with('seccion', $seccion);
			}			
	    }
	    else {
			Session::flash('danger', 'La Unidad administrada No. ' .$un_id. ' no existe.');
			return Redirect::route('indexPlusUns', $dato->seccione_id);	    	
	    }
	}
 
    /*************************************************************************************
     * Despliega el registro especificado en formato formulario para edición
     ************************************************************************************/	
	public function edit($un_id)
	{
		
		//Obtiene los datos de la Unidad		
		$dato = Un::find($un_id);

		//Obtiene los datos de la Seccion
		$seccion = Seccione::find($dato->seccione_id);
		//dd($seccion->toArray());		

		if ($seccion->tipo == 1) {
			// Almacena los datos de las unidades que pertenecen a una determinada sección
			$secapto = Secapto::where('seccione_id', $dato->seccione_id)->first();
	 		return view('backend.uns.edit')
	 					->with('dato', $dato)        
						->with('secapto', $secapto) 
						->with('seccion', $seccion); 
		}
		
		elseif ($seccion->tipo == 2) {
			// Almacena los datos de las unidades que pertenecen a una determinada sección
			$secre = Secre::where('seccione_id', $dato->seccione_id)->first();
	 		
	 		return view('backend.uns.edit')
	 					->with('dato', $dato)        
						->with('secre', $secre)
						->with('seccion', $seccion) ;
		}
		
		elseif ($seccion->tipo == 3) {
			// Almacena los datos de las unidades que pertenecen a una determinada sección
			$seclced = Seclced::where('seccione_id', $dato->seccione_id)->first();
	 		
	 		return view('backend.uns.edit')
	 					->with('dato', $dato)        
						->with('seclced', $seclced)
						->with('seccion', $seccion); 
		}

		elseif ($seccion->tipo == 4) {
			// Almacena los datos de las unidades que pertenecen a una determinada sección
			$seclcre = Seclcre::where('seccione_id', $dato->seccione_id)->first();
	 		
	 		return view('backend.uns.edit')
	 					->with('dato', $dato)        
						->with('seclcre', $seclcre)
						->with('seccion', $seccion);		
		}	
	}

    /*************************************************************************************
     * Actualiza registro
     ************************************************************************************/
	public function update($id)
	{
        //dd(Input::get());
        $input = Input::all();
        $rules = array(
            //'finca'    	=> 'required',
            //'documento'	=> 'required'
        );
    
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = Un::find($id);
			$dato->finca       	 	  = Input::get('finca');
			$dato->documento     	  = Input::get('documento');
			$dato->caracteristicas    = Input::get('caracteristicas');			
			$dato->activa			  = Input::has('activa');	
			$dato->save();			
			
			// Registra en bitacoras
			$detalle =	'codigo= '.		   	  	$dato->codigo. ', '.
						'finca= '.   		  	$dato->finca. ', '.
						'documento= '.   	  	$dato->documento. ', '.
						'caracteristicas= '.    $dato->caracteristicas. ', '.
						'activa= '.	    		$dato->activa;
			
			// refresca el cache para que refleje los cambios
			Cache::forever('unsAllkey', Un::all());

			Sity::RegistrarEnBitacora(2, 'uns', $dato->id, $detalle);
			Session::flash('success', 'La Unidad administrada No. ' .$id. ' ha sido editada con éxito.');
            return Redirect::route('uns.show',$id);
		}
        return Redirect::back()->withInput()->withErrors($validation);
  	}

    /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function createungrupo($seccione_id)
	{
        //dd($seccione_id);
        $dato = Seccione::where('secciones.id', $seccione_id)
        				->join('bloques','bloques.id', '=', 'secciones.bloque_id')
        				->join('phs','phs.id', '=', 'secciones.ph_id')
        				->first(array('secciones.id as seccione_id','bloques.id as bloque_id','secciones.tipo','bloques.codigo as codigobloque','secciones.codigo as codigoseccion','phs.codigo as codigoph'));
		//dd($dato->toArray());
        
        return view('backend.uns.createungrupo')
        			->with('dato', $dato);
	}     


   /*************************************************************************************
    * Almacena un nuevo registro en la base de datos
    ************************************************************************************/	
	public function storeungrupo()
	{
        //dd(Input::all());
        $input = Input::all();
        $delpiso = Input::get('delpiso');
        $alpiso  = Input::get('alpiso');
        $letras  = explode(',', Input::get('letras'));
        //dd($alpiso, $delpiso, $letras);
        
        if ($delpiso>$alpiso) {
			Session::flash('warning', '"Del piso" no puede ser mayor que "Al piso".');
			return Redirect::back()->withInput();
        }
        
        if (Input::get('tipo')==1 or Input::get('tipo')==2 or Input::get('tipo')==3 or Input::get('tipo')==4) {
			//dd(Input::get('tipo'));
	        $rules = array(
	            'letras' 	=> 'required',
	        	'alpiso' 	=> 'Integer|Between:1,100|required',
	        	'delpiso' 	=> 'Integer|Between:1,100|required'
	        );
	    
	        $messages = [
	            'required' => 'El campo :attribute es requerido!',
	            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!',
	            'between'  => 'El valor de :attribute debe estar entre 1 y 100 pisos!'
	        ];        
	            
	        $validation = \Validator::make($input, $rules, $messages);      	

			
			if ($validation->passes())
			{
				for ($x=$delpiso; $x<=$alpiso; $x++) {
				  foreach ($letras as $letra) {
						$dato = new Un;
						$dato->piso 		= $x;	
						$dato->letra 		= strtoupper($letra);	
						$dato->seccione_id 	= Input::get('seccione_id');				
						$dato->save();					

						$dto=Un::find($dato->id);
						$dto->codigofull = $x.strtoupper($letra).'-'.strtoupper(Input::get('codigobloque')).strtoupper(Input::get('codigoseccion')).'+'.strtoupper(Input::get('codigoph')).'#'.$dato->id;	
						$dto->codigo = $x.strtoupper($letra).'-'.strtoupper(Input::get('codigobloque')).strtoupper(Input::get('codigoseccion'));	
						$dto->save();
					}
				} 

				// Registra en bitacoras
				/*$detalle =	'codigo= '.		   	  $codigo. ', '.
							'finca= '.   		  $dato->finca. ', '.
							'documento= '.   	  $dato->documento. ', '.
							'caracteristicas= '.  $dato->caracteristicas;
   
				Sity::RegistrarEnBitacora(1, 'uns', $dato->id, $detalle);*/
				Session::flash('success', 'La Unidad administrada No. ha sido agregada con éxito.');
                return Redirect::route('indexsecplus', array(Input::get('bloque_id')));
            }
	        return Redirect::back()->withInput()->withErrors($validation);
		}
	}

    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($un_id)
	{
		//dd($un_id);
		/* No se permitirá borrar aquellas Unidades administradas que cumplan con por lo menos una de siguientes condiciones:
			1. Unidades que han sido asignadas a un propietario.
			2. Unidades que no tengan historial de pagos de cuotas de mantenimiento.*/
		
		$dato = Un::find($un_id);
	    
	    //Obtiene todos los propietarios de una determinada unidad.
	    $props = null;
        //dd($props);
		
		if(!empty($props)) {
			Session::flash('success', 'La Unidad administrada ' .$dato->codigo. ' no puede ser borrada porque tiene por lo menos un propietario asignado, deberá primero borrar todos los propietarios vinculados a esta Unidad.');
		}  
		else {
			
			$dato->delete();

			// Registra en bitacoras
			$detalle =	'Borra la unidad '.	$dato->codigo. ', finca No '.  $dato->finca. ', documento '.  $dato->documento;

			Sity::RegistrarEnBitacora(3, 'uns', $dato->id, $detalle);
			Session::flash('success', 'La Unidad administrada ' .$dato->codigo. ' ha sido borrada permanentemente de la base de datos.');
		}
		return Redirect::back();
	}
}