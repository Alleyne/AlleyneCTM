<?php namespace App\Http\Controllers\catalogo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use App\library\Sity;
use URL;
use Cache;

use App\Catalogo;
use App\Ctactivo;
use App\Ctpasivo;
use App\Ctpatrimonio;
use App\Ctgasto;
use App\Ctingreso;

class CatalogosController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega el registro especificado en formato formulario sólo lectura
     ************************************************************************************/	
	public function index()
	{

	    $datos = Catalogo::where('activa', 1)->orderBy('codigo')->get();
	    //dd($datos->toArray());
	    if($datos) {
			return view('catalogo.index')->with('datos', $datos);
		}
	    else {
			Session::flash('danger', 'View no existe!');
			return Redirect::back();	    	
	    }
	}

    /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function createCuenta($id)
	{
        return view('catalogo.createCuenta')->with('id', $id);
	}     
    
    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $codigo=Input::get('codigo');
        //dd($codigo[0], Input::get('id'));
        
        $exist= Catalogo::where('codigo', $codigo)->first();
        if ($exist) {
			Session::flash('danger', 'La cuenta ' .$codigo. ' ya existe, no puede haber duplicados.');
        	return Redirect::back()->withInput();	
        }				
        elseif ($codigo[0]!=Input::get('id')) {
			Session::flash('danger', 'La cuenta ' .$codigo. ' debe comenzar con '.Input::get('id'));
        	return Redirect::back()->withInput();	
        }	
        
        if (Input::get('codigo')==1 || Input::get('codigo')==2) {
	        $rules = array(
	            'nombre'    => 'required',
	        	'codigo'    => 'required|between:7,7',
	    		'nivel1'    => 'required'
	        );
        
        } elseif (Input::get('codigo')==6) {
	        $rules = array(
	            'nombre'    => 'required',
	        	'codigo'    => 'required|between:7,7',
	    		'nombre_factura'    => 'required'
	        );    
        
        } else {
 	        $rules = array(
	            'nombre'    => 'required',
	        	'codigo'    => 'required|between:7,7'
	        );
        }
        
        $messages = [
            'required' => 'El campo :attribute es requerido!',
            'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
			$dato = new Catalogo;

	        if ($codigo[0]=='1' || $codigo[0]=='2') {
				$dato->nombre       	   = Input::get('nombre');
				$dato->codigo		       = Input::get('codigo');
				$dato->tipo			  	   = Input::get('id');
				$dato->nivel1		  	   = Input::get('nivel1');
				$dato->save();	
				
				// Registra en bitacoras
				$detalle =	'nombre= '.		    $dato->nombre. ', '.
							'codigo= '.   		$dato->codigo. ', '.
	    					'nivel1= '.   		$dato->nivel1. ', '.
	    					'tipo= '.		    $dato->tipo;

	        } elseif ($codigo[0]=='3' || $codigo[0]=='4') {
				$dato->nombre       	   = Input::get('nombre');
				$dato->codigo		       = Input::get('codigo');
				$dato->tipo			  	   = Input::get('id');
				$dato->save();	
				
				// Registra en bitacoras
				$detalle =	'nombre= '.		    $dato->nombre. ', '.
							'codigo= '.   		$dato->codigo. ', '.
	    					'tipo= '.		    $dato->tipo;

	        } elseif ($codigo[0]=='6') {
				$dato->nombre       	   = Input::get('nombre');
				$dato->codigo		       = Input::get('codigo');
				$dato->tipo			  	   = Input::get('id');
				$dato->nombre_factura  	   = Input::get('nombre_factura');
				$dato->save();	
				
				// Registra en bitacoras
				$detalle =	'nombre= '.		    $dato->nombre. ', '.
							'codigo= '.   		$dato->codigo. ', '.
	    					'nombre_factura= '.	$dato->nombre_factura. ', '.
	        				'tipo= '.		    $dato->tipo;
	        }

			Sity::RegistrarEnBitacora(12, 'cuentas', $dato->id, $detalle);
			Session::flash('success', 'La cuenta "' .$dato->nombre. '" ha sido creada con éxito.');
			return Redirect::route('catalogos.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}

}