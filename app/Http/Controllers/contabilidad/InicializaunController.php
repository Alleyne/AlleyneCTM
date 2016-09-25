<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session, Cache;;
use Carbon\Carbon;
use App\library\Sity;

use App\Pcontable;
use App\Un;
use App\Seccione;
use App\Blqadmin;
use App\Ctdasm;
use App\Secapto;
use App\Ctdiario;
use App\Catalogo;

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

  	return view('contabilidad.inicializaun.createInicializacion')->with('un_id', $un_id);     	
	}	

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/  
  public function storeinicializacion()
  {
    //dd(Input::all());
    $input = Input::all();

    if (Input::get('meses')>0 && Input::get('monto')==0) {
      Session::flash('danger', '<< ERROR >> Esta tratando de inicializar la unidad con '.Input::get('meses').' meses y monto total adeudado igual a cero!');     
      return Redirect::back()->withInput();
    }
    
    $rules = array(
        'meses'       => 'Required|Numeric|min:0',
        'monto'       => 'required|Numeric|min:0',
        'anticipados'  => 'required|Numeric|min:0'
    );

    $messages = [
      'required'       => 'Informacion requerida!',
      'numeric'        => 'Solo se admiten valores numericos!',
      'min'            => 'No pueden haber valores negativos!'
    ];         
    //dd($rules, $messages);

    $validation = \Validator::make($input, $rules, $messages);        

    if ($validation->passes())
    {
      // encuentra los datos de la unidad y la marca la unidad como inicializada
      $un= Un::find(Input::get('un_id'));      
      //dd($un->toArray());      
      $un->inicializada= 1;
      $un->save();
      
      // encuentra la fecha del periodo contable mas antiguo abierto
      $periodo= Pcontable::where('cerrado', 0)->orderBy('id', 'asc')->first();
      //dd($periodo->fecha);      
      
      if (Input::get('meses')>0) {
        
        $monto= floatval(Input::get('monto'));
        $meses= floatval(Input::get('meses'));
        $sumaMontoMensual=0;

        // convierte la fecha string a carbon/carbon
        $fecha= Carbon::parse($periodo->fecha);   
        //dd($fecha);
        
        $seccion= Seccione::find($un->seccione_id);
        $blqAdmin= Blqadmin::where('bloque_id', $seccion->bloque_id)->first();
        $secapto= Secapto::where('seccione_id', $seccion->id)->first();
        //dd($secapto->toArray());
       
        for ($x= 1; $x <= Input::get('meses'); $x++) {

          $fecha= $fecha->subMonth();
          $month= $fecha->month;    
          $year=  $fecha->year; 

          // determina el periodo al que corresponde la fecha de pago    
          $pdo= Sity::getMonthName($month).'-'.$year;  
          //dd($pdo);
          
          // calcula diferencia en centavos entre el total registrado de todos los meses y el total adeudado
          for ($y= 1; $y <= $meses; $y++) {
            $sumaMontoMensual= round($sumaMontoMensual,2)+ round($monto/$meses,2);
          }
    
          $diferencia= round($monto-$sumaMontoMensual,2);
          //dd($sumaMontoMensual, $diferencia);      
          
          // Registra facturacion mensual de la unidad en estudio en el Ctdiario auxiliar de servicios de mantenimiento
          $dto= new Ctdasm;
          $dto->pcontable_id     = $periodo->id;
          $dto->fecha            = $fecha;
          $dto->ocobro           = $un->codigo.' '.$pdo;
          $dto->diafact          = $secapto->d_registra_cmpc;                
          $dto->mes_anio         = $pdo;
          $dto->detalle          = 'Cuota de mantenimiento Unidad No ' . Input::get('un_id');
          
          if ($x==1 && $diferencia>0) {
            $dto->importe        = round($monto/$meses,2)+abs($diferencia);
          } elseif ($x==1 && $diferencia<0) {
            $dto->importe        = round($monto/$meses,2)-abs($diferencia);
          } else {
            $dto->importe        = round($monto/$meses,2);
          }
          
          $dto->f_vencimiento    = Carbon::createFromDate($year, $month, 1)->endOfMonth();
          $dto->recargo          = 0;
          $dto->recargo_pagado   = 1;
          $dto->f_descuento      = $fecha;   
          $dto->bloque_id        = $seccion->bloque_id;
          $dto->seccione_id      = $seccion->id;
          $dto->blqadmin_id      = $blqAdmin->id;
          $dto->un_id            = Input::get('un_id');
          $dto->recargo_siono      = 1;
          $dto->save(); 
        } 
                
        // contabiliza la inicializacion
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo->id;
        $dato->fecha         = Carbon::today();
        $dato->detalle       = 'Cuota de mantenimiento por cobrar';
        $dato->debito        = Input::get('monto');
        $dato->save(); 

        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo->id;
        $dato->detalle = '   Ingresos por cuota de mantenimiento';
        $dato->credito = Input::get('monto');
        $dato->save(); 

        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo->id;
        $dato->detalle = 'Para registrar ingresos por cuotas de mantenimiento por inicializacion, '.$un->codigo;
        $dato->save(); 
      
        // Registra facturacion mensual de la unidad en cuenta 'Cuota de mantenimiento por cobrar' 1120.00
        Sity::registraEnCuentas(
                $periodo->id, // periodo                      
                'mas',  // aumenta
                1,      // cuenta id
                1,      // '1120.00',
                Carbon::createFromDate($year, $month, 1),   // fecha
                'Inicializa Cuota de mantenimiento por cobrar '.$un->codigo, // detalle
                Input::get('monto') // monto
               );

        // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento' 4120.00
        Sity::registraEnCuentas(
                $periodo->id, // periodo
                'mas',    // aumenta
                4,        // cuenta id
                3,        //'4120.00'
                Carbon::createFromDate($year, $month, 1),   // fecha
                'Inicializa Ingreso por cuota de mantenimiento '.$un->codigo, // detalle
                Input::get('monto') // monto
               );
      } 
      
      // contabiliza pago anticipado si existe
      if (Input::get('anticipados')>0) {
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo->id;
        $dato->fecha         = Carbon::today();
        $dato->detalle       = Catalogo::find(8)->nombre;
        $dato->debito        = Input::get('anticipados');
        $dato->save(); 

        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo->id;
        $dato->detalle = Catalogo::find(5)->nombre;
        $dato->credito = Input::get('anticipados');
        $dato->save(); 

        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id = $periodo->id;
        $dato->detalle = 'Para registrar pago anticipados por inicializacion, '.$un->codigo;
        $dato->save(); 
        
        Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, Carbon::today(), Catalogo::find(8)->nombre.' '.Un::find(Input::get('un_id'))->codigo, Input::get('anticipados'), Input::get('un_id'));
        Sity::registraEnCuentas($periodo->id, 'mas', 2, 5, Carbon::today(), Catalogo::find(5)->nombre.' unidad '.Un::find(Input::get('un_id'))->codigo, Input::get('anticipados'), Input::get('un_id'));
      } 

      // Registra en bitacoras
      $detalle =  'Inicializa unidad '.Input::get('un_id').' con '.Input::get('meses').' meses adeudados, saldo de '.Input::get('monto').' y B/.'.Input::get('anticipados').' en pagos anticipados.';  
            
      Sity::RegistrarEnBitacora(1, 'ctdasms', Null, $detalle);
      Session::flash('success', 'Unidad '.$un->codigo. ' ha sido inicializada con éxito.');     
      return redirect(Cache::get('indexunallkey'));
    }

    return Redirect::back()->withInput()->withErrors($validation);
  }    
} 