<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo, DB;
use App\library\Sity;
use Session;

use App\Org;
use App\Serviproducto;
use App\Catalogo;
use App\Bitacora;

class OrgsController extends Controller
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
		$datos = Org::all();
		//dd($datos->toArray());
        
    //return response()->json($datos->toArray());
 		return view('core.orgs.index')->with('datos', $datos);
	}   

  /*************************************************************************************
   * Despliega formulario para crear un nuevo registro
   ************************************************************************************/	
	public function create()
	{
    return view('core.orgs.create');
	} 

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function serviproductosPorOrg($org_id)
	{
		//Encuentra todos los serviproductos asignadas a una determinada Organizacion
		$datos = Org::find($org_id)->serviproductos;
		$datos_1 = $datos->pluck('nombre', 'id')->all(); 
    //dd($datos_1);

    //Obtiene solo los productos registrados en la tabla serviproductos
    $datos_2= Serviproducto::where('tipo', 0)->where('activo', 1)->orderBy('nombre')->get();
    $datos_2= $datos_2->pluck('nombre', 'id')->all();       
    //dd($datos_1, $datos_2);
        
    //Obtiene solo los servicios registrados en la tabla serviproductos
    $datos_3= Serviproducto::where('tipo', 1)->where('activo', 1)->orderBy('nombre')->get();
    $datos_3= $datos_3->pluck('nombre', 'id')->all();       
    //dd($datos_1, $datos_2, $datos_3);    

    // Subtrae de la lista total serviproductos registrados toda aquellos
    // que ya están asignadas a una organizacion
    // para evitar asignar serviproductos previamente asignadas
		$productos = array_diff($datos_2, $datos_1);		
		//dd($productos);  
    
    $servicios = array_diff($datos_3, $datos_1);    
    //dd($datos_1, $productos, $servicios); 		
 		
    return view('core.orgs.serviproductosPorOrg')
 				->with('datos', $datos)
 				->with('org_id', $org_id)
 				->with('productos', $productos)
        ->with('servicios', $servicios);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
	{
    
    $this->validate($request, array(
        'nombre'    	=> 'required',
        'ruc'    			=> 'required',    
        'digitov'    	=> 'required'
        ));
		
		$org= new Org;
		$org->nombre = $request->nombre;		
		$org->tipo = $request->tipo;					
		$org->ruc = $request->ruc;		
		$org->digitov = $request->digitov;
		$org->pais = $request->pais;		
		$org->provincia = $request->provincia;					
		$org->distrito = $request->distrito;		
		$org->corregimiento = $request->corregimiento;
		$org->comunidad = $request->comunidad;
		$org->telefono = $request->telefono;		
		$org->celular = $request->celular;					
		$org->email = $request->email;		
		$org->imagen = $request->imagen;
		$org->save();
		
		Session::flash('success', 'La organizacion ' .$org->nombre. ' ha sido creada con exito');
    return redirect()->route('orgs.index');
	}

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //dd($id);
      $org = Org::find($id);
      return view('core.orgs.edit')->withOrg($org);
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

    $org = Org::find($id);

    $this->validate($request, array(
        'nombre'    	=> 'required',
        'ruc'    			=> 'required',    
        'digitov'    	=> 'required'
        ));

		$org->nombre = $request->nombre;		
		$org->tipo = $request->tipo;					
		$org->ruc = $request->ruc;		
		$org->digitov = $request->digitov;
		$org->pais = $request->pais;		
		$org->provincia = $request->provincia;					
		$org->distrito = $request->distrito;		
		$org->corregimiento = $request->corregimiento;
		$org->comunidad = $request->comunidad;
		$org->telefono = $request->telefono;		
		$org->celular = $request->celular;					
		$org->email = $request->email;		
		$org->imagen = $request->imagen;
		$org->save();

		Session::flash('success', 'La organizacion ' .$org->nombre. ' ha sido actualizada!');
    return redirect()->route('orgs.index');
  }

/*  public function delete($id)
  {
      $comment = Comment::find($id);
      return view('blog.comments.delete')->withComment($comment);
  }*/

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      
		$org = Org::find($id);
		//dd($org->facturas);
	
    if (!$org->facturas->isEmpty()) {
			Session::flash('warning', 'La organizacion ' .$org->nombre. ' no puede ser borrada porque tiene por lo menos una factura acreditada.');
			return back();
		}
    
    if (!$org->catalogos->isEmpty()) {
			Session::flash('warning', 'La organizacion ' .$org->nombre. ' no puede ser borrada porque tiene por lo menos una cuenta contable asignada.');
			return back();
		}    

    $org->delete();

		// Registra en bitacoras
		$detalle =	'Borra la Organizacion '. $org->nombre;       
		
		//Sity::RegistrarEnBitacora(3, 'bloques', $bloque->id, $detalle);
		Session::flash('success', 'La Organizacion ' .$org->nombre. ' ha sido borrada permanentemente de la base de datos.');			
		return back();
  }

  /*************************************************************************************
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   *************************************************************************************/
  public function vinculaServiproductoStore(Request $request) {
    //dd($request->toArray());
    
    DB::beginTransaction();
    try {

      if ($request->tipo_radios == 0) {
        $this->validate($request, array(
          'producto_id' => 'Required'
        ));
      
        $org= Org::find($request->org_id);
        $org->serviproductos()->attach($request->producto_id);   
        
        $productoNombre = Serviproducto::find($request->producto_id)->nombre;
        $detalle =  'Vincula '.$productoNombre. ' a proveedor '. $org->nombre;

        // Registra en bitacoras
        Sity::RegistrarEnBitacora(10, 'org_serviproducto',1, $detalle);
        Session::flash('success', $productoNombre.' ha sido vinculada a el proveedor '. $org->nombre);
        
        DB::commit();     
        return back();

      } else {
        $this->validate($request, array(
          'servicio_id' => 'Required'
        ));
        
        $org= Org::find($request->org_id);
        $org->serviproductos()->attach($request->servicio_id);   
        
        $servicioNombre = Serviproducto::find($request->servicio_id)->nombre;
        $detalle =  'Vincula '.$servicioNombre. ' a proveedor '. $org->nombre;
        
        // Registra en bitacoras
        Sity::RegistrarEnBitacora(10, 'org_serviproducto',1, $detalle);
        Session::flash('success', $servicioNombre.' ha sido vinculada a el proveedor '. $org->nombre);
        
        DB::commit();     
        return back();
      }
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo OrgsController.vinculaServiproductoStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back();
    }

  }

  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function desvincularServiproducto($org_id, $serviproducto_id)
	{
		
		$org=Org::find($org_id);
		$org->serviproductos()->detach($serviproducto_id);		

		$serviproductonombre = Serviproducto::find($serviproducto_id)->nombre;

		// Registra en bitacoras
		$detalle =	'Desvincula serviproducto '.$serviproductonombre. ' del proveedor '. $org->nombre;
		Sity::RegistrarEnBitacora(11, 'kresultadocta_org',1, $detalle);
		
		Session::flash('success', 'El serviproducto ' .$serviproductonombre. ' ha sido desvinculado del proveedor '. $org->nombre);
		return redirect()->route('serviproductosPorOrg', $org_id);
 	}
}