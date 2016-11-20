<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo;
use App\library\Sity;
use Session;

use App\Jd;
use App\Bitacora;

class JdsController extends Controller
{
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
    
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function index()
	{
		//Encuentra todas las juntas directivas registradas 
		$datos = Jd::all();
        // Debugbar::info($datos->toArray());
        
        //return response()->json($datos->toArray());
 		return view('core.jds.index')->with('datos', $datos);
	}	

  /*************************************************************************************
   * Despliega el registro especificado en formato formulario sólo lectura
   ************************************************************************************/	
	public function show($id)
	{
    $dato = Jd::find($id);
    if(!empty($dato)) {
		//return response()->json($dato);  //api
		return view('core.jds.show')->with('dato', $dato);
		
		} else {
		//return response()->json(["mensaje" => "Junta directiva no existe!"]); //api			
		Session::flash('danger', 'La Junta Directiva No. ' .$id. ' no existe.');
		
		//return redirect()->route('jds.index');    	
		//return redirect()->action('JdsController@show', ['id' => 1]);
		return redirect()->action('JdsController@index');
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
    $input = Input::all();
    $rules = array(
        'nombre'    	=> 'required'
    );

    $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
    ];        
        
    $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = new Jd;
			$dato->nombre       = Input::get('nombre');
			$dato->descripcion  = Input::get('descripcion');
			$dato->save();	
			
			// Actualiza la ruta de la imagen del Administrador
			$img_path = Jd::find($dato->id);
			$img_path->imagen_L = "assets/img/jds/jd_".$dato->id.".jpg";
			$img_path->save();			
			
			// Registra en bitacoras
			$detalle =	'Crea la Junta directiva '.	$dato->nombre;
 			Sity::RegistrarEnBitacora(1, 'jds', $dato->id, $detalle);
			
			//return response()->json(["mensaje" => 'La Junta Directiva ' .$dato->nombre. ' ha sido creada con éxito.']); //api
			Session::flash('success', 'La Junta Directiva ' .$dato->nombre. ' ha sido creada con éxito.');
			return redirect()->route('jds.index');
		}
    return back()->withInput()->withErrors($validation);
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
    $input = Input::all();
    $rules = array(
        'nombre' => 'required'
    );

    $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
    ];        
        
    $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			$dato = Jd::find($id);
			$dato->nombre       = Input::get('nombre');
			$dato->descripcion  = Input::get('descripcion');
			$dato->save();			
			
			// Registra en bitacoras
			$detalle =	'Edita el nombre de la junta directiva a '.	$dato->nombre;

			Sity::RegistrarEnBitacora(2, 'jds', $dato->id, $detalle);
			
			//return response()->json(["mensaje" => 'La Junta Directiva ' .$id. ' ha sido editada con éxito.']); //api
			Session::flash('success', 'La Junta Directiva ' .$id. ' ha sido editada con éxito.');
			return redirect()->route('jds.index');
		}
    return back()->withInput()->withErrors($validation);
  }
  
  
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($jd_id)
	{
		//dd($jd_id);
		/*No se permitirá borrar aquellas Juntas directivas que cumplan con por lo menos una de siguientes condiciones:
			1. Juntas directivas que tengan por lo menos un Bloque asignado a la misma.
			2. Juntas directivas que tengan por lo menos un periodo.*/
		
		$dato = Jd::find($jd_id);
		
		// Revisa si hay algún bloque asignado a la junta directiva
		$bloques= Bloque::where('jd_id', $jd_id)->first();
		//dd($bloques);

		// Revisa si hay algún usuario vinculado a la junta directiva
		/*$users= Jds_users_group::where('jd_id', $jd_id)
							   ->first();*/		
		//dd($users->toArray());

		if(!empty($bloques)) {
			Session::flash('success', 'La Junta Directiva ' .$dato->nombre. ' no puede ser borrada porque tiene uno o más bloques asignadoa a la misma.');
			return redirect()->route('admin.jds.index');	
		}
		
		elseif (!empty($users)) {
			//return response()->json(["mensaje" => 'La Junta Directiva ' .$dato->nombre. ' no puede ser borrada porque tiene uno o más periodos asignados a la misma.']); //api
			Session::flash('success', 'La Junta Directiva ' .$dato->nombre. ' no puede ser borrada porque tiene uno o más periodos asignadoa a la misma.');
			return redirect()->route('admin.jds.index');	
		}

		else {
			$dato->delete();

			// Registra en bitacoras
			$detalle =	'Borra la Justa directiva '. $dato->nombre;
			Sity::RegistrarEnBitacora(3, 'jds', $dato->id, $detalle);
			
			//return response()->json(["mensaje" => 'La Junta Directiva ' .$dato->nombre. ' ha sido borrada permanentemente de la base de datos.']); //api
			Session::flash('success', 'La Junta Directiva ' .$dato->nombre. ' ha sido borrada permanentemente de la base de datos.');			
			return redirect()->route('admin.jds.index');	
		}
	}
}