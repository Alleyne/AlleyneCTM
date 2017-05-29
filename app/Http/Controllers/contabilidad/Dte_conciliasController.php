<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\library\Sity;
use Session, DB;

use App\Dte_concilia;

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

      $rules = array(
        'detalle' => 'Required',
        'monto' => 'required|Numeric|min:0.01'    
      );

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
     
        if (Input::get('secciones_radios') == 1 && Input::get('DteLibroMas_radios') == 1 && Input::get('DteLibroMenos_radios') == 1 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
          // salva nota de credito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'mas';          
          $dato->tipo = 'n/c';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();

        } elseif (Input::get('secciones_radios') == 1 && Input::get('DteLibroMas_radios') == 2 && Input::get('DteLibroMenos_radios') == 1 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
          // salva ajuste por error mas
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'mas';          
          $dato->tipo = 'aj_lmas';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif (Input::get('secciones_radios') == 2 && Input::get('DteLibroMas_radios') == 1 && Input::get('DteLibroMenos_radios') == 1 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
          // salva nota de debito
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'menos';          
          $dato->tipo = 'n/d';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif (Input::get('secciones_radios') == 2 && Input::get('DteLibroMas_radios') == 1 && Input::get('DteLibroMenos_radios') == 2 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
          // salva ajuste por error menos
          $dato = new Dte_concilia;
          $dato->seccion = 'libro';    
          $dato->masmenos = 'menos';          
          $dato->tipo = 'aj_lmenos';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save();
        
        } elseif (Input::get('secciones_radios') == 3 && Input::get('DteLibroMas_radios') == 1 && Input::get('DteLibroMenos_radios') == 1 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
          // salva el depositos en transito
          $dato = new Dte_concilia;
          $dato->seccion = 'banco';    
          $dato->masmenos = 'mas';
          $dato->tipo = 'd_transito';         
          $dato->detalle = Input::get('detalle');
          $dato->monto = Input::get('monto');
          $dato->concilia_id = Input::get('concilia_id');
          $dato->save(); 

        } elseif (Input::get('secciones_radios') == 4 && Input::get('DteLibroMas_radios') == 1 && Input::get('DteLibroMenos_radios') == 1 && Input::get('DteBanvoMas_radios') == 1 && Input::get('DteBancoMenos_radios') == 1) {
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
        return redirect()->route('concilias.show', $dato->concilia_id);      
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
  public function show($periodo_id)
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
