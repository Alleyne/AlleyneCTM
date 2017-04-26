<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, Cache, DB;
use Carbon\Carbon;
use App\library\Sity;
use App\library\Ppago;

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
      $pdo= Pcontable::find(1);
      
      if (!$pdo) {
        Session::flash('danger', '<< ERROR >> Antes de inicializar una unidad usted debera crear el primer periodo contable!');     
        return back()->withInput();
      }

      if (Input::get('meses')>0 && Input::get('monto')==0) {
        Session::flash('danger', '<< ERROR >> Esta tratando de inicializar la unidad con '.Input::get('meses').' meses y monto total adeudado igual a cero!');     
        return back()->withInput();
      }
      
      $rules = array(
        'meses'       => 'Required|integer|min:1',
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
        // encuentra los datos de la unidad y marca la unidad como inicializada
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
        
        $seccion= Seccione::find($un->seccione_id);
        $blqAdmin= Blqadmin::where('bloque_id', $seccion->bloque_id)->first();
        $secapto= Secapto::where('seccione_id', $seccion->id)->first();
        //dd($secapto->toArray());          
        
        $meses= floatval(Input::get('meses'));  // 3        5       3
        $monto= floatval(Input::get('monto'));  // 250.00   355.00  272.00            
        
        $n= round(($monto/$meses),2) * $meses;  // 249.99   355.00  272.01
        $cuotaMesual= round(($monto/$meses),2); //  83.33    71.00  90.67
        $fraction = round($monto - $n,2);       //   0.01     0.00  -0.01
        //dd($n, $cuotaMesual, $fraction);

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
          
          if ($x==1 && $fraction>=0) {
            $dto->importe = $cuotaMesual + abs($fraction);

          } elseif ($x==1 && $fraction<0) {
            $dto->importe = $cuotaMesual - abs($fraction);
          
          } else {
            $dto->importe = $cuotaMesual;
          }

          $dto->f_vencimiento    = Carbon::createFromDate($year, $month, 1)->endOfMonth();
          $dto->recargo          = 0;
          $dto->recargo_pagado   = 1;
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

        // contabiliza pago anticipado si existe
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo->id;
        $dato->fecha         = $f_periodo;
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
        
        Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, $f_periodo, 'Pago anticipado por inicializacion del sistema, unidad '.Un::find(Input::get('un_id'))->codigo, Input::get('anticipados'), Input::get('un_id'));
        Sity::registraEnCuentas($periodo->id, 'mas', 2, 5, $f_periodo, 'Pago anticipado por inicializacion del sistema', Input::get('anticipados'), Input::get('un_id'));
      
        // Encuentra todas las unidades activas
        $uns= Un::where('activa', 1)->get();
        // dd($uns->toArray());

        // verifica si se puede realizar pagos de cuotas o recargos utilizando solamente
        // el contenido de la cuenta de pagos anticipados de la unidad.        
        Ppago::iniciaPago(Input::get('un_id'), $f_periodo, $periodo->id, $periodo->periodo);

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
        Session::flash('warning', ' Ocurrio un error en el modulo InicializaunController.storeinicializacion, la transaccion ha sido cancelada! '.$e->getMessage());

        return back()->withInput()->withErrors($validation);
    }
  }    
} 