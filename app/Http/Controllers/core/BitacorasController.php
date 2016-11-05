<?php
namespace App\Http\Controllers\core;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Input, Redirect, Str;
use Cache;

use App\Bitacora;
use App\Accione;


class BitacorasController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function index()
	{
	    $bitacoras = Bitacora::orderBy('id', 'desc')
	    		   			 ->with('accione')
	    		   			 ->get();
	    
	    //dd($bitacoras->toArray());	

		return view('core.bitacoras.index')->with('bitacoras', $bitacoras);	
	}

    /*************************************************************************************
     * Despliega el registro especificado en formato formulario solo lectura
     ************************************************************************************/	
	public function show($id)
	{
		//Encuentra la imagen del usuario y se la envia al view
		$imagen= Auth::user()->imagen;
		$usuario=Cache::get('userFullNamekey');
		//dd($usuario);	

		//Encuentra la bitacora con el id = $id
		$bitacora= Bitacora::find($id);
		//dd($bitacora);
		
		//Encuentra la acción de la bitacora
		$accion= Accione::find($bitacora->accione_id);		
		//dd($accion);		
		
		return view('core.bitacoras.show')->with('bitacora', $bitacora)
												  ->with('imagen',$imagen)
												  ->with('accion', $accion->nombre)
												  ->with('usuario', $usuario);
	}
}