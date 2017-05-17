<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session, DB;
use App\library\Sity;
use Carbon\Carbon;

use App\Cajachica;
use App\Org;
use App\User;
use App\Pcontable;
use App\Ctdiario;
use App\Catalogo;
use App\Dte_cajachica;
use App\Desembolso;

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
    //dd($datos);

    if (!$datos->isEmpty()) {
      $cerrada = $datos->last()->cerrada;
    } else {
      $cerrada = 1;
    }
    //dd($datos->toArray(), $cerrada);
         
    return view('contabilidad.cajachicas.index')
          ->with('cerrada', $cerrada)
          ->withDatos($datos);
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
        'doc_no'=> 'required',
        'monto' => 'required|Numeric|min:1',
        'aprueba_id' => 'required',
        'user_id' => 'required|Numeric|min:1'
      ));

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado', 0)->orderBy('id')->first();
      //dd($periodo);

      if (!$periodo) {
        Session::flash('danger', '<< ERROR >> No existe ningun periodo contable abierto, debera crear un nuevo periodo para poder crear o reabrir una Caja chica!');
        return back()->withInput();
      }

      // convierte la fecha string a carbon/carbon
      $f_cajachica = Carbon::parse($request->fecha);   
      $month= $f_cajachica->month;    
      $year= $f_cajachica->year; 
      
      // determina el periodo al que corresponde la fecha de pago    
      $pdo= Sity::getMonthName($month).'-'.$year;
      
      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);

      // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
      if ($pdo != $periodo->periodo) {
        Session::flash('danger', '<< ERROR >> Solamente se permite crear una Caja chica si su fecha correspondan al periodo vigente de '.$periodo->periodo);
        return back()->withInput();
      }
      
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
      $cajachica->monto_maximo = $request->monto_maximo;
      $cajachica->save();
      
      // registra nuevo detalle en dte_cajachicas
      $dte_cajachica = new Dte_cajachica;
      $dte_cajachica->fecha = $request->fecha;
      $dte_cajachica->descripcion = 'Se abre nueva caja chica #'.$cajachica->id.', chq #'.$request->doc_no;
      $dte_cajachica->doc_no = $request->doc_no;
      $dte_cajachica->aumenta = $request->monto;
      $dte_cajachica->saldo = $request->monto + $montoActual;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = $cajachica->id;
      $dte_cajachica->save();   
      
      Sity::RegistrarEnBitacora($dte_cajachica, $request, 'Dte_cajachica', 'Se abre nueva caja chica');      
     
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
      $dato->detalle = 'Para registrar deposito bancario por creacion de caja chica #'.$cajachica->id.', chq #'.$request->doc_no;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        30,
        $request->fecha,
        'Para registrar deposito bancario por creacion de caja chica #'.$cajachica->id.', chq #'.$request->doc_no,
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
        'Para registrar deposito bancario por creacion de caja chica #'.$cajachica->id.', chq #'.$request->doc_no,
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
  public function aumentarCajachicaCreate($cajachica_id)
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.aumentar')
          ->with('cajachica_id', $cajachica_id)
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
      $dte_cajachica->descripcion = 'Se aumenta saldo de caja chica #'.$request->cajachica_id.', chq #'.$request->doc_no;
      $dte_cajachica->doc_no = $request->doc_no;
      $dte_cajachica->aumenta = $request->monto;
      $dte_cajachica->saldo = $request->monto + $montoActual;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = $request->cajachica_id;
      $dte_cajachica->save();   
      
      Sity::RegistrarEnBitacora($dte_cajachica, $request, 'Dte_cajachica', 'Se aumenta saldo de caja chica');
      
      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::find($request->cajachica_id);
      $cajachica->saldo = $request->monto + $montoActual;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha        = $request->fecha;
      $dato->detalle      = 'Caja chica';
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
      $dato->detalle = 'Para registrar deposito bancario por aumento de caja chica #'.$request->cajachica_id.', chq #'.$request->doc_no;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        30,
        $request->fecha,
        'Para registrar deposito bancario por aumento de caja chica #'.$request->cajachica_id.', chq #'.$request->doc_no,
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
        'Para registrar deposito bancario por aumento de caja chica #'.$request->cajachica_id.', chq #'.$request->doc_no,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se aumentado el saldo de la caja chica #'.$request->cajachica_id);
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
  public function disminuirCajachicaCreate($cajachica_id)
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.disminuir')
          ->with('cajachica_id', $cajachica_id)
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
      $dte_cajachica->descripcion = 'Se disminuye saldo de caja chica #'.$request->cajachica_id;
      $dte_cajachica->disminuye = $request->monto;
      $dte_cajachica->saldo = $montoActual - $request->monto;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = $request->cajachica_id;
      $dte_cajachica->save();   
     
      Sity::RegistrarEnBitacora($dte_cajachica, $request, 'Dte_cajachica', 'Se disminuye saldo de caja chica');

      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::find($request->cajachica_id);
      $cajachica->saldo = $montoActual - $request->monto;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha = $request->fecha;
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
      $dato->detalle = 'Para registrar deposito bancario por disminucion de caja chica #'.$request->cajachica_id;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        8,
        $request->fecha,
        'Para registrar deposito bancario por disminucion de caja chica #'.$request->cajachica_id,
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
        'Para registrar deposito bancario por disminucion de caja chica #'.$request->cajachica_id,
        $request->monto,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se disminuyo el saldo de la caja chica #'.$request->cajachica_id);
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
  public function cerrarCajachicaCreate($cajachica_id)
  {
    //Encuentra todos los usuarios candidatos a responsabilizarse por el nuevo fondo de caja chica
    $usuarios = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
    //dd($usuarios);
    
   return view('contabilidad.cajachicas.cerrar')
          ->with('cajachica_id', $cajachica_id)
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

       // no permite cerrar la caja chica si tiene algun desemboso por aprobar
      $desembolsos = Desembolso::where('cajachica_id', $request->cajachica_id)->where('aprobado', 0)->first();

      if ($desembolsos) {
        Session::flash('warning', 'No se puede cerrar la presente Caja chica ya que la misma tiene por lo menos un desembolso sin aprobar!');
        return redirect()->route('cajachicas.index');
      }
      
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
      $dte_cajachica->descripcion = 'Cierre de caja chica #'.$request->cajachica_id;
      $dte_cajachica->disminuye =  $montoActual;
      $dte_cajachica->saldo = 0;
      $dte_cajachica->aprueba_id = $request->aprueba_id;
      $dte_cajachica->aprueba = User::find($request->aprueba_id)->nombre_completo;
      $dte_cajachica->cajachica_id = $request->cajachica_id;
      $dte_cajachica->save();   
     
      Sity::RegistrarEnBitacora($dte_cajachica, $request, 'Dte_cajachica', 'Cierre de caja chica');

      // Actualiza el saldo de cajachicas
      $cajachica = Cajachica::find($request->cajachica_id);
      $cajachica->saldo = 0;
      $cajachica->f_cierre = $request->fecha;
      $cajachica->cerrada = 1;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->fecha = $request->fecha;
      $dato->detalle = Catalogo::find(8)->nombre;
      $dato->debito = $montoActual;
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle      = 'Caja chica';
      $dato->credito      = $montoActual;
      $dato->save(); 
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = 'Para registrar deposito bancario por cierre de caja chica #'.$request->cajachica_id;
      $dato->save(); 
      
      Sity::registraEnCuentas(
        $periodo->id,
        'mas', 
        1,
        8,
        $request->fecha,
        'Para registrar deposito bancario por cierre de caja chica #'.$request->cajachica_id,
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
        'Para registrar deposito bancario por cierre de caja chica #'.$request->cajachica_id,
        $montoActual,
        Null,
        Null,
        Null,
        Null,
        Null,
        Null
      );

      Session::flash('success', 'Se cierra la caja chica #'.$request->cajachica_id);
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.cerrarCajachicaStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }


 }