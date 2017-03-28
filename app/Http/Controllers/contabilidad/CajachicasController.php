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
      return view('contabilidad.cajachicas.create');
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
        'monto' => 'required|Numeric|min:1'
      ));

      $cajachica = new Cajachica;

      $montoActual = Cajachica::all()->last();
      if ($montoActual) {
        $montoActual= $montoActual->monto;
      } else {
        $montoActual= 0;
      }

      //dd($montoActual);

      $cajachica->fecha = $request->fecha;
      
      if ($request->radios == 1) {
        $cajachica->aumento = $request->monto;
        $cajachica->monto = $request->monto + $montoActual;
      } else {
        $cajachica->disminucion = $request->monto;
        $cajachica->monto = $montoActual - $request->monto;
      }
      $cajachica->save();

      // encuentra el periodo mas antiguo abierto
      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
      //dd($periodo);
      
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id  = $periodo->id;
      $dato->fecha         = $request->fecha;
      if ($request->radios == 1) {
        $dato->detalle     = 'Caja chica';
        $dato->debito      = $request->monto;
      
      } elseif ($request->radios == 2) {
        $dato->detalle     =  Catalogo::find(8)->nombre;
        $dato->debito      = $request->monto;
      }
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      if ($request->radios == 1) {
        $dato->detalle = Catalogo::find(8)->nombre;
        $dato->credito = $request->monto;
      
      } elseif ($request->radios == 2) {
        $dato->detalle = 'Caja chica';
        $dato->credito = $request->monto;
      }
      $dato->save(); 

      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id = $periodo->id;
      if ($request->radios == 1) {
        $dato->detalle = 'Para registrar el cheque No. y aumentar el fondo de caja chica';
      
      } elseif ($request->radios == 2) {
        $dato->detalle = 'Para registrar el cheque No. y disminuir el fondo de caja chica';
      }
      $dato->save(); 
      
      if ($request->radios == 1) {
        Sity::registraEnCuentas(
          $periodo->id,
          'mas', 
          1,
          30,
          $request->fecha,
          'Caja chica',
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
          Catalogo::find(8)->nombre,
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
          Catalogo::find(8)->nombre,
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
          'Caja chica',
          $request->monto,
          Null,
          Null,
          Null,
          Null,
          Null,
          Null
        );
      }      

      Session::flash('success', 'Se ha fijado un nuevo monto para la caja chica!');
      DB::commit();  
      return redirect()->route('cajachicas.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
      return back()->withInput()->withErrors($validation);
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