<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use Validator;
use Carbon\Carbon;

use App\Ctgasto;
use App\Ctingreso;
use App\Pcontable;
use App\Bitacora;

class PcontablesController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function index()
	{
        //Obtiene todos los Periodos contables.
        $datos = Pcontable::All();
        //dd($datos->toArray());
  		
  		return view('contabilidad.pcontables.index')->with('datos', $datos);     	
	}	
} 