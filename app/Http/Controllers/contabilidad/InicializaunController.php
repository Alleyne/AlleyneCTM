<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, Cache, DB;
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
    DB::beginTransaction();
    try {
      
      //dd(Input::all());
      $input = Input::all();

      if (Input::get('meses')>0 && Input::get('monto')==0) {
        Session::flash('danger', '<< ERROR >> Esta tratando de inicializar la unidad con '.Input::get('meses').' meses y monto total adeudado igual a cero!');     
        return back()->withInput();
      }
      
      $pdo= Pcontable::find(1);
      if (!$pdo) {
        Session::flash('danger', '<< ERROR >> Antes de inicializar una unidad usted debera crear el primer periodo contable!');     
        return back()->withInput();
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
        
        // convierte la fecha string a carbon/carbon
        $f_periodo= Carbon::parse($periodo->fecha);   
        //dd($f_periodo);      
        
        // hace una copia de la fecha original para evitar que $f_periodo cambie
        $fecha = clone $f_periodo;
        
        if (Input::get('meses')>0) {
          
          $monto= floatval(Input::get('monto'));
          $meses= floatval(Input::get('meses'));
          $sumaMontoMensual=0;
          
          $seccion= Seccione::find($un->seccione_id);
          $blqAdmin= Blqadmin::where('bloque_id', $seccion->bloque_id)->first();
          $secapto= Secapto::where('seccione_id', $seccion->id)->first();
          //dd($secapto->toArray());
          
          // calcula diferencia en centavos entre el total registrado de todos los meses y el total adeudado
          for ($y= 1; $y <= $meses; $y++) {
            $sumaMontoMensual= round($sumaMontoMensual,2)+ round($monto/$meses,2);
          }
    
          $diferencia= round($monto - $sumaMontoMensual,2);
          //dd($sumaMontoMensual, $diferencia);          
          
          for ($x= 1; $x <= $meses; $x++) {

            $fecha= $fecha->subMonth();
            $month= $fecha->month;    
            $year= $fecha->year; 
            $dia= $secapto->d_registra_cmpc == 1 ? '01' : '16';
            
            // Registra facturacion mensual de la unidad en estudio en el Ctdiario auxiliar de servicios de mantenimiento
            $dto= new Ctdasm;
            $dto->pcontable_id     = $periodo->id;
            $dto->fecha            = $fecha;
            $dto->ocobro           = $un->codigo.' '.Sity::getMonthName($month).$dia.'-'.$year;
            $dto->diafact          = $secapto->d_registra_cmpc;                
            $dto->mes_anio         = Sity::getMonthName($month).'-'.$year;
            $dto->detalle          = 'Cuota de mantenimiento Unidad No ' . Input::get('un_id');
            
            if ($x==1 && $diferencia>=0) {
              $dto->importe = round($monto/$meses,2) + abs($diferencia);

            } elseif ($x==1 && $diferencia<0) {
              $dto->importe = round($monto/$meses,2) - abs($diferencia);
            
            } else {
              $dto->importe = round($monto/$meses,2);
            }

            $dto->f_vencimiento    = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $dto->recargo          = 0;
            $dto->recargo_pagado   = 0;
            $dto->f_descuento      = $fecha;   
            $dto->bloque_id        = $seccion->bloque_id;
            $dto->seccione_id      = $seccion->id;
            $dto->blqadmin_id      = $blqAdmin->id;
            $dto->un_id            = Input::get('un_id');
            $dto->recargo_siono    = 1;
            $dto->save(); 
          } 
                  
          // contabiliza la inicializacion
          $dato = new Ctdiario;
          $dato->pcontable_id  = $periodo->id;
          $dato->fecha         = $f_periodo;
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
                  $f_periodo,   // fecha
                  'Inicializa Cuota de mantenimiento por cobrar '.$un->codigo, // detalle
                  Input::get('monto') // monto
                 );

          // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento' 4120.00
          Sity::registraEnCuentas(
                  $periodo->id, // periodo
                  'mas',    // aumenta
                  4,        // cuenta id
                  3,        //'4120.00'
                  $f_periodo,   // fecha
                  'Inicializa Ingreso por cuota de mantenimiento '.$un->codigo, // detalle
                  Input::get('monto') // monto
                 );
        } 
        
        // contabiliza pago anticipado si existe
        if (Input::get('anticipados')>0) {
          
          // registra en Ctdiario principal
          $dato = new Ctdiario;
          $dato->pcontable_id  = $periodo->id;
          $dato->fecha         = $f_periodo;
          $dato->detalle       = 'Pago anticipado por inicializacion del sistema';
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
          
          Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, $f_periodo, 'Anticipado por inicializacion del sistema '.Un::find(Input::get('un_id'))->codigo, Input::get('anticipados'), Input::get('un_id'));
          Sity::registraEnCuentas($periodo->id, 'mas', 2, 5, $f_periodo, Catalogo::find(5)->nombre.' unidad '.Un::find(Input::get('un_id'))->codigo, Input::get('anticipados'), Input::get('un_id'));
        } 

        // Registra en bitacoras
        $detalle =  'Inicializa unidad '.Input::get('un_id').' con '.Input::get('meses').' meses adeudados, saldo de '.Input::get('monto').' y B/.'.Input::get('anticipados').' en pagos anticipados.';  
              
        Sity::RegistrarEnBitacora(1, 'ctdasms', Null, $detalle);
        DB::commit();          
        Session::flash('success', 'Unidad '.$un->codigo. ' ha sido inicializada con éxito.');     
        
        return redirect(Cache::get('indexunallkey'));
      }
      return back()->withInput()->withErrors($validation);
    
    } catch (\Exception $e) {
        DB::rollback();
        Session::flash('warning', ' Ocurrio un error en el modulo InicializaunController.storeinicializacion, la transaccion ha sido cancelada!');

        return back()->withInput()->withErrors($validation);
    }
  }    
} 