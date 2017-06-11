<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Session, DB;
use Carbon\Carbon;

use App\Dte_concilia;
use App\Pcontable;
use App\Ctdiario;
use App\Catalogo;

class Dte_conciliasController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /***********************************************
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   ***********************************************/
  public function store(Request $request)
  {
      //
  }

   /***********************************************
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   ***********************************************/
  public function addDetalleConciliacion()
  {

    DB::beginTransaction();
    try {
      //dd(Input::all());
      $input = Input::all();
      $condicion = Input::get('secciones_radios').Input::get('DteLibroMas_radios').Input::get('DteLibroMenos_radios');
      //dd($condicion);
      
      if ($condicion == '111') {
        $rules = array(
          'catalogo4_id' => 'Required',
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );

      } elseif ($condicion == '211') {
        $rules = array(
          'catalogo6_id' => 'Required',
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );

      } elseif ($condicion == '311') {
        $rules = array(
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );
      
      } elseif ($condicion == '411') {
        $rules = array(
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );
      }

      $messages = [
          'required'      => 'Informacion requerida!',
          'before'        => 'La fecha de la factura debe ser anterior o igual a fecha del dia de hoy!',
          'digits_between'=> 'El numero de la factura debe tener de uno a diez digitos!',
          'numeric'       => 'Solo se admiten valores numericos!',
          'date'          => 'Fecha invalida!',
          'min'           => 'Se requiere un valor mayor que cero!'
      ];                
        
      $validation = \Validator::make($input, $rules, $messages);  
      if ($validation->passes())
      {
        
        if ($condicion == '111') {
          // salva nota de credito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'mas';          
          $dato->tipo = 'n/c';         
          $dato->catalogo_id = Input::get('catalogo4_id');
          $dato->cuenta = Catalogo::find(Input::get('catalogo4_id'))->nombre;  
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '211') {
          // salva nota de debito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'menos';          
          $dato->tipo = 'n/d';         
          $dato->catalogo_id = Input::get('catalogo6_id');
          $dato->cuenta = Catalogo::find(Input::get('catalogo6_id'))->nombre; 
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '311') {
          // salva el depositos en transito
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'mas';
          $dato->tipo = 'd_transito';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save(); 
        
        } elseif ($condicion == '411') {
          // salva el cheques en circulacion
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'menos';
          $dato->tipo = 'chq_circulacion';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        }

        //Session::flash('success', 'Nota de credito ha sido agregada con éxito.');
        DB::commit();       
        return redirect()->route('concilias.show', Input::get('concilia_id'));      
      }

      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);    
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo ConciliasController.addNotaDeCredito, la transaccion ha sido cancelada! '.$e->getMessage());
      return back()->withInput();
    }
  }  

 
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function contabilizaConcilia($concilia_id, $pcontable_id)
  {
    //dd($concilia_id, $pcontable_id);
    DB::beginTransaction();
    try {
      $dte_concilias = Dte_concilia::where('concilia_id', $concilia_id)->get();
      //dd($dte_concilias->toArray());      

      // encuentra la fecha del inicio de periodo
      $pdoFechaInicio = Pcontable::find($pcontable_id)->fecha;
      $pdoFechaInicio = Carbon::parse($pdoFechaInicio)->endOfMonth();
      //dd($pdoFechaInicio);
      
      //==================================================================
      // SECCION LIBRO MAS
      //==================================================================      
      // encuentra las notas de credito
      $ncs = $dte_concilias->where('tipo', 'n/c');
      //dd($ncs->toArray());    
      
      // registra en libros la seccion Notas de credito de la conciliacion
      if ($ncs) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $ncs->sum('monto');
        $diario->credito = Null;
        $diario->save();      

        // registra en el mayor
        Sity::registraEnCuentas($pcontable_id, 'mas', 1, 8, $pdoFechaInicio, 'Notas de credito', $ncs->sum('monto'), Null, Null, Null, Null, Null);            
 
        foreach ($ncs as $nc) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $nc->cuenta.' - '.$nc->detalle;
          $diario->debito  = Null;
          $diario->credito = $nc->monto;
          $diario->save();
        
          // registra en el mayor
          $cuenta = Catalogo::find($nc->catalogo_id);
          Sity::registraEnCuentas($pcontable_id, 'menos', $cuenta->tipo, $cuenta->id, $pdoFechaInicio, $nc->detalle, $nc->monto, Null, Null, Null, Null, Null);            
        }
        
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Notas de credito del mes. (Conciliacion)';
        $diario->save();
      }

      //==================================================================
      // SECCION LIBRO MENOS
      //==================================================================      
      // encuentra las notas de debito
      $nds = $dte_concilias->where('tipo', 'n/d');
      //dd($nds->toArray());
      
      // registra en libros la seccion Notas de credito de la conciliacion
      if ($nds) {     
        $i = 0;
        foreach ($nds as $nd) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          if ($i == 0) { $diario->fecha = $pdoFechaInicio; }
          $diario->detalle = $nd->cuenta.' - '.$nd->detalle;
          $diario->debito  = $nd->monto;
          $diario->credito = Null;
          $diario->save();
          
          // registra en el mayor
          $cuenta = Catalogo::find($nd->catalogo_id);
          Sity::registraEnCuentas($pcontable_id, 'mas', $cuenta->tipo, $cuenta->id,  $pdoFechaInicio, $nd->detalle, $nd->monto, Null, Null, Null, Null, Null);                    

          $i++;
        }
        
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = Null;
        $diario->credito = $nds->sum('monto');
        $diario->save(); 
        
        // registra en el mayor
        Sity::registraEnCuentas($pcontable_id, 'menos', 1, 8, $pdoFechaInicio, 'Notas de debito', $nds->sum('monto'), Null, Null, Null, Null, Null);            

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Notas de debito del mes. (Conciliacion)';
        $diario->save();
      }

      Sity::RegistrarEnBitacoraEsp($dte_concilias, 'dte_concilias', 1, 'Elabora y aprueba conciliacion');
      
      Session::flash('success', 'Conciliacion a hido registrada y aprobada con exito.');
      DB::commit();       
      return redirect()->route('pcontables.index');

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en Dte_controller@contabilizaConcilia, la transaccion ha sido cancelada!');
      return back()->withInput();
    }  

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

    DB::beginTransaction();
    try {
      
      $dato = Dte_concilia::find($id);    
      $dato->delete();      

      // Registra en bitacoras
      Sity::RegistrarEnBitacora($dato, Null, 'Dte_concilia', 'Elimina Nota de credito de conciliacion');   
      
      //Session::flash('success', 'La Nota de credito ha sido borrada permanentemente de la base de datos');
      DB::commit();       
      return Redirect()->route('concilias.show', $dato->concilia_id);

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo Dte_concilias.destroy, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }   
  }
}
