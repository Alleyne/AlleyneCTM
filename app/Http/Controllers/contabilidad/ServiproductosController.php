<?php
namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB;
use App\library\Sity;

use App\Serviproducto;
use App\Dte_ecajachica;
use App\Detallefactura;
use App\Org_serviproducto;
use App\Catalogo;
use App\Bitacora;

class ServiproductosController extends Controller
{
  public function __construct()
  {
    $this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function index()
	{
		//Encuentra todos los roles registrados 
		$datos = Serviproducto::all();
    
    // encuentra las cuentas de gastos    
    $cuentas = Catalogo::where('tipo', 6)->get();
    $cuentas = $cuentas->pluck('nombre', 'id')->all();       
    //dd($datos->toArray(), $cuentas);

    return view('contabilidad.serviproductos.index')
          ->with('cuentas', $cuentas)
          ->with('datos', $datos);
	}   

  /*************************************************************************************
   * Despliega formulario para crear un nuevo registro
   ************************************************************************************/	
	public function edit($serviproducto_id)
	{
    $dato = Serviproducto::find($serviproducto_id);
    return view('contabilidad.serviproductos.edit')->with('dato', $dato);
	} 

  
  /*************************************************************************************
  * Actualiza registro
  ************************************************************************************/
  public function update($id) {
    
    DB::beginTransaction();
    try {
      //dd(Input::get());
      $input = Input::all();
      $rules = array(
          'nombre'      => 'required'
      );

      $messages = [
          'required' => 'El campo :attribute es requerido!',
          'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
      ];        
          
      $validation = \Validator::make($input, $rules, $messages);        

      if ($validation->passes()) {
        $dato = Serviproducto::find($id);
        $dato->nombre = Input::get('nombre');
        $dato->activo = Input::has('activo');;
        $dato->save();      
        
        // Registra en bitacoras
        $detalle =  'nombre= '. $dato->nombre;

        Sity::RegistrarEnBitacora(2, 'serviproductos', $dato->id, $detalle);
        Session::flash('success', 'El nombre del serviproducto No. ' .$id. ' ha sido editado con éxito.');
        
        DB::commit();
        return redirect()->route('serviproductos.index');
      }
      Session::flash('danger', 'Se encontraron errores en su formulario, intente nuevamente.');
      return back()->withInput()->withErrors($validation);
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', 'Ocurrio un error en el modulo SeviproductosController.update, la transaccion ha sido cancelada! '.$e->getMessage());
      return back()->withInput();
    }  

  }


  /*************************************************************************************
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   *************************************************************************************/
  public function store(Request $request)
	{
    //dd($request->toArray());
    
    DB::beginTransaction();
    try {

      $this->validate($request, array(
        'nombre'    	 => 'required',
        'catalogo_id' => 'required'
      ));
  		
  		$serviproducto= new Serviproducto;
  		$serviproducto->nombre = $request->nombre;		
  		$serviproducto->tipo = $request->tipo_radios;					
  		$serviproducto->catalogo_id = $request->catalogo_id;		
  		$serviproducto->save();
  		
  		DB::commit();  
      Session::flash('success', 'El serviproducto ' .$serviproducto->nombre. ' ha sido creada con exito');
      return redirect()->route('serviproductos.index');
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo ServiproductosController.store, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }
  }

  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/  
  public function destroy($serviproducto_id)
  {

    DB::beginTransaction();
    try {
      $dato1 = Detallefactura::where('serviproducto_id', $serviproducto_id)->first();    
      $dato2 = Dte_ecajachica::where('serviproducto_id', $serviproducto_id)->first(); 
      $dato3 = Org_serviproducto::where('serviproducto_id', $serviproducto_id)->first(); 
      
      if($dato1) {
        Session::flash('warning', '<< ATENCION >> Este serviproducto no puede ser eliminado porque ya tiene historial en por lo menos una facturas de egreso de caja general, favor desactivarlo si no lo desea utilizar!');
        return Redirect()->route('serviproductos.index');
      
      } elseif ($dato2) {
        Session::flash('warning', '<< ATENCION >> Este serviproducto no puede ser eliminado porque ya tiene historial en por lo menos una facturas de egreso de caja chica, favor desactivarlo si no lo desea utilizar!');
        return Redirect()->route('serviproductos.index');
      
      } elseif($dato3) {
        Session::flash('warning', '<< ATENCION >> Este serviproducto no puede ser eliminado porque ya que esta vinculado a una cuenta contable, favor desactivarlo si no lo desea utilizar!');
        return Redirect()->route('serviproductos.index');
      }

      else {
        $dato = Serviproducto::find($serviproducto_id);
        $dato->delete();      

        // Registra en bitacoras
        $detalle =  'Elimina serviproducto '.$dato->nombre;
        
        Sity::RegistrarEnBitacora(3, 'serviproductos', $dato->id, $detalle);
        Session::flash('success', 'El serviproducto' .$dato->nombre. ' ha sido eliminado permanentemente de la base de datos.');
        DB::commit();       
        return Redirect()->route('serviproductos.index');
      }

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo ServiproductosController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());

      return back();
    }   
  }



}