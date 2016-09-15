<?php namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Grupo;
use App\library\Sity;
use Redirect, Session;

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
 		return view('backend.orgs.index')->with('datos', $datos);
	}   

    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function catalogosPorOrg($org_id)
	{
		//Encuentra todos las cuentas de gastos asignadas a una determinada Organizacion
		$datos = Org::find($org_id)->catalogos;
		$datos_1 = $datos->lists('nombre_factura', 'id');
		$datos_1 = $datos_1->toArray();
	    //dd($datos_1);

	    //Obtiene todas las cuentas de gastos registrados en la base de datos
 	    $datos_2= Catalogo::where('enfactura',1)
                    ->orderBy('nombre_factura')
                    ->get();
	    $datos_2= $datos_2->lists('nombre_factura', 'id');       
		$datos_2 = $datos_2->toArray();
	    //dd($datos_1, $datos_2);
        
        // Subtrae de la lista total de cuentas de gastos registrados toda aquellas
        // que ya están asignadas a una organizacion
        // para evitar asignar cuentas previamente asignadas
		$ksubcuentas = array_diff($datos_2, $datos_1);		
		//dd($ksubcuentas);  
 		
 		return view('backend.orgs.catalogosPorOrg')
 				->with('datos', $datos)
 				->with('org_id', $org_id)
 				->with('ksubcuentas', $ksubcuentas);
	}

    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
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
			$detalle =	'Vincula subcuenta '.	$kcuentasname->nombre_factura. ' a proveedor '. $org->nombre;
 			Sity::RegistrarEnBitacora(10, 'ksubcuenta_org',1, $detalle);
			
			Session::flash('success', 'La subcuenta ' .$kcuentasname->nombre_factura. ' ha sido vinculada a el proveedor '. $org->nombre);
			return Redirect::back();
		}
        return Redirect::back()->withInput()->withErrors($validation);
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
		
		Session::flash('success', 'La subcuenta ' .$kcuenta->nombre. ' ha sido desvinculada del proveedor '. $org->nombre);
		return Redirect::route('catalogosPorOrg', $org_id);
 	}
}