<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo;
use App\library\Sity;
use Session;

use App\Org;
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
	public function catalogosPorOrg($org_id)
	{
		//Encuentra todos las cuentas de gastos asignadas a una determinada Organizacion
		$datos = Org::find($org_id)->catalogos;
		$datos_1 = $datos->pluck('nombre_factura', 'id')->all(); 
    //dd($datos_1);

    //Obtiene todas las cuentas de gastos registrados en la base de datos
    $datos_2= Catalogo::where('enfactura',1)
                ->orderBy('nombre_factura')
                ->get();
    $datos_2= $datos_2->pluck('nombre_factura', 'id')->all();       
    //dd($datos_1, $datos_2);
        
    // Subtrae de la lista total de cuentas de gastos registrados toda aquellas
    // que ya están asignadas a una organizacion
    // para evitar asignar cuentas previamente asignadas
		$ksubcuentas = array_diff($datos_2, $datos_1);		
		//dd($ksubcuentas);  
 		
 		return view('core.orgs.catalogosPorOrg')
 				->with('datos', $datos)
 				->with('org_id', $org_id)
 				->with('ksubcuentas', $ksubcuentas);
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
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function vinculaCuentaStore()
	{
    //dd(Input::all());
    $input = Input::all();
    $rules = array(
        'id'    	=> 'required'
    );

    $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
    ];        
        
    $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
			$org=Org::find(Input::get('org_id'));
			$org->catalogos()->attach(Input::get('id'));		
			
			$kcuentasname = Catalogo::find(Input::get('id'));

			// Registra en bitacoras
			$detalle =	'Vincula cuenta '.	$kcuentasname->nombre_factura. ' a proveedor '. $org->nombre;
 			Sity::RegistrarEnBitacora(10, 'ksubcuenta_org',1, $detalle);
			
			Session::flash('success', 'La cuenta ' .$kcuentasname->nombre_factura. ' ha sido vinculada a el proveedor '. $org->nombre);
			return back();
		}
    return back()->withInput()->withErrors($validation);
	}


  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function desvincularSubcuenta($org_id, $kresultadocta_id)
	{
		
		$org=Org::find($org_id);
		$org->catalogos()->detach($kresultadocta_id);		

		$kcuenta = Catalogo::find($kresultadocta_id);

		// Registra en bitacoras
		$detalle =	'Desvincula subcuenta '.$kcuenta->nombre. ' del proveedor '. $org->nombre;
		Sity::RegistrarEnBitacora(11, 'kresultadocta_org',1, $detalle);
		
		Session::flash('success', 'La cuenta ' .$kcuenta->nombre. ' ha sido desvinculada del proveedor '. $org->nombre);
		return redirect()->route('catalogosPorOrg', $org_id);
 	}
}