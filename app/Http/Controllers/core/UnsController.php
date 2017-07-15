<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Session, Validator, Image, URL, Cache, DB;

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
		$i = 0;		
		
		foreach ($datos as $dato) {
			$ctdasm= Ctdasm::where('un_id', $dato->id)->get(); 			
			$estatus1 = $ctdasm->where('pagada', 0)
													->first();

			$estatus2 = $ctdasm->Where('recargo_siono', 1)
													->Where('recargo_pagado', 0)
													->first();
			
			$estatus3 = $ctdasm->where('extra_siono', 1)
							 						->where('extra_pagada', 0)
							 						->first();

		  if(!is_null($estatus1) || !is_null($estatus2) || !is_null($estatus3)) {
		    	$datos[$i]['estatus']='Moroso';
			}
	    elseif(is_null($estatus1) && is_null($estatus2) && is_null($estatus3)) {
	    	$datos[$i]['estatus']='Paz y salvo';
	    }
  
	    $props = Prop::where('un_id', $dato->id)
	    		   ->join('users','users.id','=','props.user_id')
	    		   ->select('cedula','nombre_completo')
	    		   ->get();
			//dd($props->toArray());			

			$propietarios = "";
			foreach ($props as $prop) {
				if ($propietarios == "") {
					$propietarios = $prop->cedula.' '.$prop->nombre_completo;
				}
				else {
					$propietarios = $propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;					
				}
			}
			//dd($propietarios);		    
		    $datos[$i]['propietarios'] = $propietarios;
		    $i ++;
		}       
        //dd($propietarios);
		
		Cache::forever('goto_1', URL::full());
		
		//dd($datos->toArray());        
    return view('core.uns.indexunall')
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
    $seccion = Seccione::find($seccione_id);
    //dd($secciones->toArray());        

    //Obtiene los datos del Bloques 
    $bloque = Bloque::find($seccion->bloque_id);
    //dd($bloques->toArray());        
    
	    //Obtiene los datos de la Junta Directiva
    $jd = Jd::find($bloque->jd_id);		
    //dd($jd->toArray());      
	    
	    //Obtiene los datos del o los adiministradores del bloque
		$blqadmins = Blqadmin::where('bloque_id', $bloque->id)
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
		$i = 0;		
		foreach ($datos as $dato) {
			$estatus = Ctdasm::where('un_id', $dato->id)
							 ->where('pagada', '0')
							 ->first();
		    
	    if(!empty($estatus)) {
	    	$datos[$i]['estatus'] = 'Moroso';
			
			} else {
		    	$datos[$i]['estatus'] = 'Paz y salvo';
		  }
		    
	    // recorta el codigo de la unidad
	    //$dato->codigo=strtok($dato->codigo, '+');
	    
	    $props=Prop::where('un_id', $dato->id)
	    		   ->join('users','users.id','=','props.user_id')
	    		   ->select('cedula','nombre_completo')
	    		   ->get();
			//dd($props->toArray());			

			$propietarios = "";
			foreach ($props as $prop) {
				if ($propietarios == "") {
					$propietarios = $prop->cedula.' '.$prop->nombre_completo;
				
				} else {
					$propietarios = $propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;					
				}
			}
			//dd($propietarios);		    
	    $datos[$i]['propietarios']=$propietarios;
	    $i ++;
		}       
    //dd($propietarios);
		
		Cache::forever('goto_1', URL::full());
    return view('core.uns.indexunplus')
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
    //$dato = Un::find($un_id);
    //dd($dato->toArray());
    
		// trae los datos de las unidades almacenados en cache
		$datos = Cache::get('unsAllkey');

		// encuentra los datos de una unidad en especial
		$dato = $datos->where('id', $un_id)->first();
		//dd($dato);

    Cache::forever('indexunallkey', URL::full());

    if(!is_null($dato)) {
			//obtiene los datos de la sección
			$seccion = Seccione::find($dato->seccione_id);	
			//dd($seccion->toArray());			
			
			// encuentra a los propietarios
			$props = Prop::where('un_id', $un_id)
						->with('user')
						->get();
		
			$secapto = Secapto::where('seccione_id', $dato->seccione_id)->first();
			//dd($secapto->toArray());
		
			return view('core.uns.show')
			            ->with('dato', $dato)
			            ->with('secapto', $secapto)
			            ->with('props', $props)
			            ->with('seccion', $seccion);
    
    } else {
			Session::flash('danger', 'La Unidad administrada No. ' .$un_id. ' no existe.');
			return redirect()->route('indexPlusUns', $dato->seccione_id);	    	
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

		// Almacena los datos de las unidades que pertenecen a una determinada sección
		$secapto = Secapto::where('seccione_id', $dato->seccione_id)->first();
 		return view('core.uns.edit')
 					->with('dato', $dato)        
					->with('secapto', $secapto) 
					->with('seccion', $seccion); 
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
				$dato->caracteristicas  = Input::get('caracteristicas');			
				$dato->activa			  		= Input::has('activa');	
				Sity::RegistrarEnBitacora($dato, Input::get(), 'Un', 'Actualiza unidad');
				$dato->save();			
			
				// refresca el cache para que refleje los cambios
				Cache::forever('unsAllkey', Un::all());

  			DB::commit();

				Session::flash('success', 'La Unidad administrada No. ' .$id. ' ha sido editada con éxito.');
	      return redirect()->route('uns.show',$id);
			}
	    return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en UnsController.update, la transaccion ha sido cancelada!');
	    return back()->withInput();
	  }
  }

  /*************************************************************************************
   * Despliega formulario para crear un nuevo registro
   ************************************************************************************/	
	public function createungrupo($seccione_id)
	{
    //dd($seccione_id);
    $dato = Seccione::where('secciones.id', $seccione_id)
    				->join('bloques','bloques.id', '=', 'secciones.bloque_id')
    				->first(array('secciones.id as seccione_id','bloques.id as bloque_id','secciones.tipo','bloques.codigo as codigobloque','secciones.codigo as codigoseccion'));
		//dd($dato->toArray());
        
   	$codigo_ph = Seccione::find($seccione_id)->bloque->jd->codigo;     
    //dd($codigo_ph);
    
    return view('core.uns.createungrupo')
    			->with('codigo_ph', $codigo_ph)
    			->with('dato', $dato);
	}     


  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function storeungrupo()
	{
      
	  DB::beginTransaction();
	  try {
	    //dd(Input::all());
	    $input 	 = Input::all();
	    $delpiso = Input::get('delpiso');
	    $alpiso  = Input::get('alpiso');
	    $letras  = explode(',', Input::get('letras'));
	    //dd($alpiso, $delpiso, $letras);
	      
	    if ($delpiso > $alpiso) {
				Session::flash('warning', '"Del piso" no puede ser mayor que "Al piso".');
				return back()->withInput();
	    }
	      
			//dd(Input::get('tipo'));
      $rules = array(
        'letras' 	=> 'required',
      	'alpiso' 	=> 'Integer|Between:1,100|required',
      	'delpiso' => 'Integer|Between:1,100|required'
      );
  
      $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!',
        'between'  => 'El valor de :attribute debe estar entre 1 y 100 pisos!'
      ];        
          
      $validation = \Validator::make($input, $rules, $messages);      	
			
			if ($validation->passes()) {
				for ($x = $delpiso; $x <= $alpiso; $x++) {
				  foreach ($letras as $letra) {
						$dato = new Un;
						$dato->piso 				= $x;	
						$dato->letra 				= strtoupper($letra);	
						$dato->seccione_id 	= Input::get('seccione_id');				
						$dato->save();					

						$dto = Un::find($dato->id);
						$dto->codigofull = $x.strtoupper($letra).'-'.strtoupper(Input::get('codigobloque')).strtoupper(Input::get('codigoseccion')).'+'.strtoupper(Input::get('codigo_ph')).'#'.$dato->id;	
						$dto->codigo =     $x.strtoupper($letra).'-'.strtoupper(Input::get('codigobloque')).strtoupper(Input::get('codigoseccion'));	
						$dto->save();
					}
				} 
  
				//Sity::RegistrarEnBitacora(1, 'uns', $dato->id, $detalle);
  			
  			Cache::forever('unsAllkey', Un::all());
  			DB::commit();
				
				Session::flash('success', 'Se ha registrado y enumerado un nuevo grupo de unidades.');
        return redirect()->route('indexsecplus', array(Input::get('bloque_id')));
      }
	    return back()->withInput()->withErrors($validation);

	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en UnsController.storeungrupo, la transaccion ha sido cancelada!');
	    return back()->withInput();
	  }
	}

  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/	
	public function destroy($un_id)
	{
		
	  DB::beginTransaction();
	  try {
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

				//Sity::RegistrarEnBitacora(3, 'uns', $dato->id, $detalle);
				DB::commit();
				Session::flash('success', 'La Unidad administrada ' .$dato->codigo. ' ha sido borrada permanentemente de la base de datos.');
			}
			return back();
	  
	  } catch (\Exception $e) {
	    DB::rollback();
	    Session::flash('warning', ' Ocurrio un error en UnsController.destroy, la transaccion ha sido cancelada!');
	    return back();
	  }
	}

}