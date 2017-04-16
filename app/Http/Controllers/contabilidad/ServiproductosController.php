<?php
namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB;

use App\Serviproducto;
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
	public function create()
	{
    //return view('core.orgs.create');
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

  
}