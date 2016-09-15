<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\library\Sity;
use Input, Session, Redirect, Str, Carbon\Carbon, URL;
use Validator, View;
use Debugbar;

use App\Un;
use App\Ctmayore;

class CtmayoresController extends Controller {
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }
    
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
  ************************************************************************************/ 
/*  public function verUnActivos($un_id) {
        
    $un=Un::find($un_id);
    $datos = Ctmayore::where('un_id', $un_id)->where('tipo', 1)->get();
    //dd($datos->toArray());
  
  return \View::make('contabilidad.ctmayores.verUnActivos')
              ->with('datos', $datos)
              ->with('un', $un);
  } */

 /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
  ************************************************************************************/ 
/*  public function verUnPasivos($un_id) {
        
    $un=Un::find($un_id);
    $datos = Ctmayore::where('un_id', $un_id)->where('tipo', 2)->get();
    //dd($datos->toArray());
  
  return \View::make('contabilidad.ctmayores.verUnActivos')
              ->with('datos', $datos)
              ->with('un', $un);
  } */
 
 /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
  ************************************************************************************/ 
/*  public function verUnIngresos($un_id) {
        
    $un=Un::find($un_id);
    $datos = Ctmayore::where('un_id', $un_id)->where('tipo', 4)->get();
    //dd($datos->toArray());
  
  return \View::make('contabilidad.ctmayores.verUnActivos')
              ->with('datos', $datos)
              ->with('un', $un);
  }*/ 
}