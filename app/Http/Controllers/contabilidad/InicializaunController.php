<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use Validator;
use Carbon\Carbon;


class InicializaunController extends Controller {
    
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function inicializaUn($un_id)
	{

  	return view('contabilidad.inicializaUn.createInicializacion')->with('un_id', $un_id);     	
	}	

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/  
  public function editainicializacion()
  {
    
    return 'estoy en proceso de editar la inicializacion';     
  } 


  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/  
  public function storeinicializacion()
  {
    
    return 'estoy en proceso de guardar inicializacion';     
  } 
} 