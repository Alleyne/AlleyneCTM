<?php
namespace App\Http\Controllers\core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo, Session, DB;
use App\library\Sity;

use App\Org;
use App\Serviproducto;
use App\Org_serviproducto;
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
		$datos_2 = Serviproducto::where('tipo', 0)->where('activo', 1)->orderBy('nombre')->get();
		$datos_2 = $datos_2->pluck('nombre', 'id')->all();       
		//dd($datos_1, $datos_2);
				
		//Obtiene solo los servicios registrados en la tabla serviproductos
		$datos_3 = Serviproducto::where('tipo', 1)->where('activo', 1)->orderBy('nombre')->get();
		$datos_3 = $datos_3->pluck('nombre', 'id')->all();       
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
		
		DB::beginTransaction();
		try {
			
			$this->validate($request, array(
				'nombre'    	=> 'required',
				'ruc'    			=> 'required',    
				'digitov'    	=> 'required'
			));
			
			$dato = new Org;
			$dato->nombre = $request->nombre;		
			$dato->tipo = $request->tipo;					
			$dato->ruc = $request->ruc;		
			$dato->digitov = $request->digitov;
			$dato->pais = $request->pais;		
			$dato->provincia = $request->provincia;					
			$dato->distrito = $request->distrito;		
			$dato->corregimiento = $request->corregimiento;
			$dato->comunidad = $request->comunidad;
			$dato->telefono = $request->telefono;		
			$dato->celular = $request->celular;					
			$dato->email = $request->email;		
			$dato->imagen = $request->imagen;
			$dato->save();

  		Sity::RegistrarEnBitacora($dato, $request, 'Org', 'Registra un nuevo proveedor');
			DB::commit();  		

			Session::flash('success', 'La organizacion ' .$dato->nombre. ' ha sido creada con exito');
			return redirect()->route('orgs.index');

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en OrgsController.store, la transaccion ha sido cancelada!');
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
		DB::beginTransaction();
		try {
		
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
  		Sity::RegistrarEnBitacora($org, $request, 'Org', 'Actualiza proveedor');
			$org->save();
			
			DB::commit();
			Session::flash('success', 'La organizacion ' .$org->nombre. ' ha sido actualizada!');
			return redirect()->route('orgs.index');

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en OrgsController.update, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
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
		
			$org = Org::find($id);
			//dd($org->facturas);
		
			if (!$org->facturas->isEmpty()) {
				Session::flash('warning', 'El proveedor ' .$org->nombre. ' no puede ser borrada porque tiene por lo menos una factura por Caja general acreditada.');
				return back();
			}
			
			if (!$org->ecajachicas->isEmpty()) {
				Session::flash('warning', 'El proveedor ' .$org->nombre. ' no puede ser borrada porque tiene por lo menos una factura por Caja chica acreditada.');
				return back();
			}
			

			if (!$org->serviproductos->isEmpty()) {
				Session::flash('warning', 'El proveedor ' .$org->nombre. ' no puede ser borrada porque tiene por lo menos un serviproducto asignado.');
				return back();
			}    

			$org->delete();
  		
  		Sity::RegistrarEnBitacora($org, Null, 'Org', 'Elimina proveedor'); 
			DB::commit(); 

			Session::flash('success', 'El proveedor ' .$org->nombre. ' ha sido borrada permanentemente de la base de datos.');			
			return back();

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en OrgController.destroy, la transaccion ha sido cancelada!');
			return back()->withInput();
		}
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
			
				$org = Org::find($request->org_id);
				$org->serviproductos()->attach($request->producto_id);   

				$productoNombre = Serviproducto::find($request->producto_id)->nombre;

				// Registra en bitacoras
				$detalle = 'El serviproducto "'.$productoNombre.'" ha sido vinculado al proveedor '. $org->nombre;
				$tabla = 'org_serviproducto';
				$registro = Org_serviproducto::all()->last()->id;
				$accion = 'Vincula producto a proveedor';
				Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

				Session::flash('success', $detalle);
				
				DB::commit();     
				return back();

			} else {
				$this->validate($request, array(
					'servicio_id' => 'Required'
				));
				
				$org = Org::find($request->org_id);
				$org->serviproductos()->attach($request->servicio_id);   
				
				$servicioNombre = Serviproducto::find($request->servicio_id)->nombre;
					
				// Registra en bitacoras
				$detalle = 'El serviproducto "'.$servicioNombre.'" ha sido vinculado al proveedor '. $org->nombre;
				$tabla = 'org_serviproducto';
				$registro = Org_serviproducto::all()->last()->id;
				$accion = 'Vincula servicio a proveedor';
				Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

				Session::flash('success', $detalle);
				
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
		DB::beginTransaction();
		try {
			// encuentra el id del registro a desvincular
			$registo_id = Org_serviproducto::where('serviproducto_id', $serviproducto_id)->where('org_id', $org_id)->first()->id;

			// procede a desvincular el serviproduto de la organizacion
			$org = Org::find($org_id);
			$org->serviproductos()->detach($serviproducto_id);		

			// encuenta el nombre del serviproducto desvinculado
			$serviproductonombre = Serviproducto::find($serviproducto_id)->nombre;

			// Registra en bitacoras
			$detalle = 'El serviproducto "'.$serviproductonombre.'" ha sido desvinculado del proveedor '. $org->nombre;
			$tabla = 'org_serviproducto';
			$registro = $registo_id;
			$accion = 'Desvincula serviproducto de proveedor';
			Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);
			
			DB::commit();   
			Session::flash('success', $detalle);
			return redirect()->route('serviproductosPorOrg', $org_id);
	
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en OrgsController.desvincularServiproducto, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}
}