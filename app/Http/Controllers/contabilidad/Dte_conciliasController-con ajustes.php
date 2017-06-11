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
      $condicion = Input::get('secciones_radios').Input::get('DteLibroMas_radios').Input::get('DteLibroMenos_radios').Input::get('DteBanvoMas_radios').Input::get('DteBancoMenos_radios');
      //dd($condicion);
      
      if ($condicion == '11111') {
        $rules = array(
          'catalogo4_id' => 'Required',
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );

      } elseif ($condicion == '12111') {
        $rules = array(
          'catalogo4_id' => 'Required',
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );

      } elseif ($condicion == '21111') {
        $rules = array(
          'catalogo6_id' => 'Required',
          'detalle' => 'Required',
          'monto' => 'required|Numeric|min:0.01'    
        );
      
      } elseif ($condicion == '21211') {
        $rules = array(
          'catalogo6_id' => 'Required',
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
        
        if ($condicion == '11111') {
          // salva nota de credito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'mas';          
          $dato->tipo = 'n/c';         
          $dato->catalogo_id = Input::get('catalogo4_id');
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();

        } elseif ($condicion == '12111') {
          // salva ajuste por error mas
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'mas';          
          $dato->tipo = 'aj_lmas';         
          $dato->catalogo_id = Input::get('catalogo4_id');
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '21111') {
          // salva nota de debito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'menos';          
          $dato->tipo = 'n/d';         
          $dato->catalogo_id = Input::get('catalogo6_id');
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '21211') {
          // salva ajuste por error menos
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'menos';          
          $dato->tipo = 'aj_lmenos';         
          $dato->catalogo_id = Input::get('catalogo6_id');
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '31111') {
          // salva el depositos en transito
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'mas';
          $dato->tipo = 'd_transito';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save(); 

        } elseif ($condicion == '31121') {
          // salva el depositos en transito
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'mas';
          $dato->tipo = 'aj_bmas';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save(); 
        
        } elseif ($condicion == '41111') {
          // salva el cheques en circulacion
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'menos';
          $dato->tipo = 'chq_circulacion';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif ($condicion == '41112') {
          // salva el cheques en circulacion
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'menos';
          $dato->tipo = 'aj_bmenos';         
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
    //dd($concilia_id);
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
      
      // encuentra los ajustes de notas de credito
      $aj_lmas = $dte_concilias->where('tipo', 'aj_lmas');
      //dd($aj_lmas->toArray());      
      
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
        Sity::registraEnCuentas($periodo, 'mas', 1, 8, $pdoFechaInicio, 'N/C', $ncs->sum('monto'), Null, Null, Null, Null, Null);            
        
        foreach ($ncs as $nc) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $nc->detalle;
          $diario->debito  = Null;
          $diario->credito = $nc->monto;
          $diario->save();
        }
        
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Notas de credito del mes. (Conciliacion)';
        $diario->save();
      }

      // registra en libros la seccion ajustes a notas de credito de la conciliacion
      if ($aj_lmas) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $aj_lmas->sum('monto');
        $diario->credito = Null;
        $diario->save();  

        foreach ($aj_lmas as $aj_lma) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $aj_lma->detalle;
          $diario->debito  = Null;
          $diario->credito = $aj_lma->monto;
          $diario->save();
        }

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para corregir errores en Notas de credito del mes. (Conciliacion)';
        $diario->save();      
      }

      //==================================================================
      // SECCION LIBRO MENOS
      //==================================================================      
      // encuentra las notas de debito
      $nds = $dte_concilias->where('tipo', 'n/d');
      //dd($nds->toArray());
      
      // encuentra los ajustes de notas de credito
      $aj_lmenos = $dte_concilias->where('tipo', 'aj_lmenos');
      //dd($aj_lmenos->toArray());      
      
      // registra en libros la seccion Notas de credito de la conciliacion
      if ($nds) {     
        $i = 0;
        foreach ($nds as $nd) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          if ($i == 0) { $diario->fecha = $pdoFechaInicio; }
          $diario->detalle = $nd->detalle;
          $diario->debito  = $nd->monto;
          $diario->credito = Null;
          $diario->save();
          $i++;
        }
        
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = Null;
        $diario->credito = $nds->sum('monto');
        $diario->save(); 
        
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Notas de debito del mes. (Conciliacion)';
        $diario->save();
      }

      // registra en libros la seccion ajustes a notas de credito de la conciliacion
      if ($aj_lmenos) {
        $i = 0;
        foreach ($aj_lmenos as $aj_lmeno) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          if ($i == 0) { $diario->fecha = $pdoFechaInicio; }
          $diario->detalle = $aj_lmeno->detalle;
          $diario->debito  = $aj_lmeno->monto;
          $diario->credito = Null;
          $diario->save();
          $i++;
        }

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = Null;
        $diario->credito = $aj_lmenos->sum('monto');
        $diario->save(); 

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para corregir errores en Notas de debito del mes. (Conciliacion)';
        $diario->save();      
      }

/*
      //==================================================================
      // SECCION BANCO MAS
      //==================================================================      
      // encuentra las depositos en transito
      $d_transitos = $dte_concilias->where('tipo', 'd_transito');
      //dd($d_transito->toArray());
      
      // encuentra los ajustes de notas de credito
      $aj_bmas = $dte_concilias->where('tipo', 'aj_bmas');
      //dd($aj_bmas->toArray());      
      
      // registra en libros la seccion Notas de credito de la conciliacion
      if ($d_transitos) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $d_transitos->sum('monto');
        $diario->credito = Null;
        $diario->save();      
     
        foreach ($d_transitos as $d_transito) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $d_transito->detalle;
          $diario->debito  = Null;
          $diario->credito = $d_transito->monto;
          $diario->save();
        }
        
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Depositos en transito del mes';
        $diario->save();
      }

      // registra en libros la seccion ajustes a notas de credito de la conciliacion
      if ($aj_bmas) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $aj_bmas->sum('monto');
        $diario->credito = Null;
        $diario->save();  

        foreach ($aj_bmas as $aj_bma) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $aj_bma->detalle;
          $diario->debito  = Null;
          $diario->credito = $aj_bma->monto;
          $diario->save();
        }

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para corregir errores en Depositos en transito del mes';
        $diario->save();      
      }
      
      //==================================================================
      // SECCION BANCO MENOS
      //==================================================================      
      // encuentra las cheques en transito o circulacion
      $chq_circulacions = $dte_concilias->where('tipo', 'chq_circulacion');
      //dd($chq_circulacion->toArray());
      
      // encuentra los ajustes de notas de credito
      $aj_bmenos = $dte_concilias->where('tipo', 'aj_bmenos');
      //dd($aj_bmas->toArray());      
      
      // registra en libros la seccion Notas de credito de la conciliacion
      if ($chq_circulacions) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $chq_circulacions->sum('monto');
        $diario->credito = Null;
        $diario->save();      
     
        foreach ($chq_circulacions as $chq_circulacion) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $chq_circulacion->detalle;
          $diario->debito  = Null;
          $diario->credito = $chq_circulacion->monto;
          $diario->save();
        }
        
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para registrar Cheques en transito del mes';
        $diario->save();
      }

      // registra en libros la seccion ajustes a notas de credito de la conciliacion
      if ($aj_bmenos) {
        // registra en el diario
        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->fecha = $pdoFechaInicio;
        $diario->detalle = Catalogo::find(8)->nombre;
        $diario->debito  = $aj_bmenos->sum('monto');
        $diario->credito = Null;
        $diario->save();  

        foreach ($aj_bmenos as $aj_bmeno) {
          // registra en el diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $pcontable_id;
          $diario->detalle = $aj_bmeno->detalle;
          $diario->debito  = Null;
          $diario->credito = $aj_bmeno->monto;
          $diario->save();
        }

        $diario = new Ctdiario;
        $diario->pcontable_id  = $pcontable_id;
        $diario->detalle = 'Para corregir errores en Cheques en transito del mes';
        $diario->save();      
      }*/

      DB::commit();

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
