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
      $responsables = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
      //dd($responsables);
      
      // determina el tipo de desc
      $cchica= Cajachica::all()->last();
      //dd($cchica);

      if (is_null($cchica)) {
        $opcion= 1;   // se desea abri nueva caja chica
    
      } elseif ($cchica && $cchica) {
        $opcion= 2;  // de desea aumentar o diminuir saldo de una caja chica
      }
      //dd($opcion);

     return view('contabilidad.cajachicas.create')
            ->with('opcion', $opcion)
            ->with('responsables', $responsables);
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
        'user_id' => 'required|Numeric|min:1'
      ));

      // determina la descripcion correcta
      $cchica= Cajachica::all()->last();
      
      if ($request->radios == 0) {
        $descripcion= 'Abre nueva caja chica, cheque no. '.$request->doc_no;

      } elseif ($request->radios == 1) {
        $descripcion= 'Aumenta saldo de caja chica, cheque no. '.$request->doc_no;

      } elseif ($request->radios == 2) {
        $descripcion= 'Disminuir saldo de caja chica';
      
      } elseif ($request->radios == 3) {
        $descripcion= 'Cerrar caja chica';

      } else {
        Session::flash('warning', 'Opcion invalida');
        return back();
      }
      //dd($descripcion);

      $cajachica = new Cajachica;
      $montoActual = Cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->saldo;
      } else {
        $montoActual= 0;
      }
      //dd($montoActual);

      $cajachica->fecha = $request->fecha;
      
      if ($request->radios == 0 || $request->radios == 1) {
        $cajachica->aumento = $request->monto;
        $cajachica->saldo = $request->monto + $montoActual;
      
      } else {
        $cajachica->disminucion = $request->monto;
        $cajachica->saldo = $montoActual - $request->monto;
      }
      
      $cajachica->descripcion = $descripcion;
      $cajachica->doc_no = $request->doc_no;
      $cajachica->responsable_id = $request->user_id;
      $cajachica->responsable = User::find($request->user_id)->nombre_completo;
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id  = $periodo->id;
      $dato->fecha         = $request->fecha;
      if ($request->radios == 0 || $request->radios == 1) {
        $dato->detalle     =  'Caja chica';
        $dato->debito      = $request->monto;
      
      } else {
        $dato->detalle     =  Catalogo::find(8)->nombre;
        $dato->debito      = $request->monto;
      }
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      if ($request->radios == 0 || $request->radios == 1) {
        $dato->detalle = Catalogo::find(8)->nombre;
        $dato->credito = $request->monto;
      
      } else {
        $dato->detalle = 'Caja chica';
        $dato->credito = $request->monto;
      }
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      $dato->detalle = $descripcion;
      $dato->save(); 
      
      if ($request->radios == 0 || $request->radios == 1) {
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

      } elseif ($request->radios == 2) {
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
      }      

      Session::flash('success', 'Se ha fijado un nuevo saldo para la caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }

  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}