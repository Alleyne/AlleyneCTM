<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session, DB;
use App\library\Sity;

use App\Cajachica;
use App\Org;
use App\User;
use App\Pcontable;
use App\Ctdiario;
use App\Catalogo;
use App\Dte_cajachica;

class CajachicasController extends Controller
{
    
  public function __construct()
  {
      $this->middleware('hasAccess');    
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $datos = Cajachica::all();
    //dd($datos->toArray());
    
    return view('contabilidad.cajachicas.index')->withDatos($datos);
  }
 
  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create()
  {
      //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
      $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
      //dd($usuarios);
      
     return view('contabilidad.cajachicas.create')
     ->with('usuarios', $usuarios);
  }
 

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    DB::beginTransaction();
    try {

      //dd($request->toArray());
      $this->validate($request, array(
        'fecha' => 'required|date',          
        'monto' => 'required|Numeric|min:1',
        'aprueba_id' => 'required',
        'user_id' => 'required|Numeric|min:1'
      ));

      $descripcion= 'Abre nueva caja chica, cheque no. '.$request->doc_no;

      // calcula el saldo actual de la caja chica
      $montoActual = Dte_cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->saldo;
      } else {
        $montoActual= 0;
      }
      //dd($montoActual);

      // registra la apertura de la nueva caja chica en cajachicas
      $cajachica = new Cajachica;
      $cajachica->f_inicio = $request->fecha;
      $cajachica->responsable_id = $request->user_id;
      $cajachica->responsable = User::find($request->user_id)->nombre_completo;
      $cajachica->saldo = $request->monto + $montoActual;
      $cajachica->save();
      
      // registra nuevo detalle en dte_cajachicas
      $dte_cajachica = new Dte_cajachica;
      $dte_cajachica->fecha = $request->fecha;
      $dte_cajachica->descripcion = $descripcion;
      $dte_cajachica->doc_no = $request->doc_no;
      $dte_cajachica->aumenta = $request->monto;
      $dte_cajachica->saldo = $request->monto + $montoActual;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = $cajachica->id;
      $dte_cajachica->save();   
     

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha        = $request->fecha;
      $dato->detalle      =  'Caja chica';
      $dato->debito       = $request->monto;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = Catalogo::find(8)->nombre;
      $dato->credito = $request->monto;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = $descripcion;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        30,
        $request->fecha,
        $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Sity::registraEnCuentas(
        $periodo->id,
        'menos', 
        1,
        8,
        $request->fecha,
        Catalogo::find(8)->nombre.', '. $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se ha abierto o reabierto una nueva caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }

  
  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */  
  public function aumentarCajachicaCreate()
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.aumentar')
          ->with('usuarios', $usuarios);
  } 
  
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function aumentarCajachicaStore(Request $request)
  {

    DB::beginTransaction();
    try {

      //dd($request->toArray());
      $this->validate($request, array(
        'fecha' => 'required|date',          
        'monto' => 'required|Numeric|min:1',
        'doc_no' => 'required',
        'aprueba_id' => 'required'
      ));

      $descripcion= 'Aumenta saldo de caja chica, cheque no. '.$request->doc_no;

      // calcula el saldo actual de la caja chica
      $montoActual = Dte_cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->saldo;
      } else {
        $montoActual= 0;
      }
      //dd($montoActual);

      // registra nuevo detalle en dte_cajachicas
      $dte_cajachica = new Dte_cajachica;
      $dte_cajachica->fecha = $request->fecha;
      $dte_cajachica->descripcion = $descripcion;
      $dte_cajachica->doc_no = $request->doc_no;
      $dte_cajachica->aumenta = $request->monto;
      $dte_cajachica->saldo = $request->monto + $montoActual;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = Cajachica::all()->last()->id;
      $dte_cajachica->save();   
     
      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::all()->last();
      $cajachica->saldo = $request->monto + $montoActual;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha        = $request->fecha;
      $dato->detalle      =  'Caja chica';
      $dato->debito       = $request->monto;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = Catalogo::find(8)->nombre;
      $dato->credito = $request->monto;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = $descripcion;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        30,
        $request->fecha,
        $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Sity::registraEnCuentas(
        $periodo->id,
        'menos', 
        1,
        8,
        $request->fecha,
        Catalogo::find(8)->nombre.', '. $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se aumentado el saldo de la caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.aumentarCajachicaStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */  
  public function disminuirCajachicaCreate()
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.disminuir')
          ->with('usuarios', $usuarios);
  } 
  
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function disminuirCajachicaStore(Request $request)
  {

    DB::beginTransaction();
    try {

      //dd($request->toArray());
      $this->validate($request, array(
        'fecha' => 'required|date',          
        'monto' => 'required|Numeric|min:1',
        'aprueba_id' => 'required'
      ));

      $descripcion= 'Para registra deposito bancario por disminucion de saldo de caja chica';

      // calcula el saldo actual de la caja chica
      $montoActual = Dte_cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->saldo;
      } else {
        $montoActual= 0;
      }
      //dd($montoActual);

      // registra nuevo detalle en dte_cajachicas
      $dte_cajachica = new Dte_cajachica;
      $dte_cajachica->fecha = $request->fecha;
      $dte_cajachica->descripcion = $descripcion;
      $dte_cajachica->disminuye = $request->monto;
      $dte_cajachica->saldo = $montoActual - $request->monto;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = Cajachica::all()->last()->id;
      $dte_cajachica->save();   
     
      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::all()->last();
      $cajachica->saldo = $montoActual - $request->monto;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha        = $request->fecha;
      $dato->detalle = Catalogo::find(8)->nombre;
      $dato->debito = $request->monto;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle      =  'Caja chica';
      $dato->credito      = $request->monto;
      $dato->save(); 
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = $descripcion;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        8,
        $request->fecha,
        Catalogo::find(8)->nombre.', '. $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );      
      
      Sity::registraEnCuentas(
        $periodo->id,
        'menos', 
        1,
        30,
        $request->fecha,
        $descripcion,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se disminuyo el saldo de la caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.disminuyeCajachicaStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }

  
  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */  
  public function cerrarCajachicaCreate()
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.cerrar')
          ->with('usuarios', $usuarios);
  } 
  
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function cerrarCajachicaStore(Request $request)
  {

    DB::beginTransaction();
    try {

      //dd($request->toArray());
      $this->validate($request, array(
        'fecha' => 'required|date',          
        'aprueba_id' => 'required'
      ));

      $descripcion= 'Para registra deposito bancario por cierre de caja chica';

      // calcula el saldo actual de la caja chica
      $montoActual = Dte_cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->saldo;
      } else {
        $montoActual= 0;
      }
      //dd($montoActual);

      // registra nuevo detalle en dte_cajachicas
      $dte_cajachica = new Dte_cajachica;
      $dte_cajachica->fecha = $request->fecha;
      $dte_cajachica->descripcion = $descripcion;
      $dte_cajachica->disminuye =  $montoActual;
      $dte_cajachica->saldo = 0;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = Cajachica::all()->last()->id;
      $dte_cajachica->save();   
     
      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::all()->last();
      $cajachica->saldo = 0;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha        = $request->fecha;
      $dato->detalle = Catalogo::find(8)->nombre;
      $dato->debito = $montoActual;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle      =  'Caja chica';
      $dato->credito      = 0;
      $dato->save(); 
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = $descripcion;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        8,
        $request->fecha,
        Catalogo::find(8)->nombre.', '. $descripcion,
        $montoActual,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );      
      
      Sity::registraEnCuentas(
        $periodo->id,
        'menos', 
        1,
        30,
        $request->fecha,
        $descripcion,
        0,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se cierra la caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.cerrarCajachicaStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }


 }