<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Org;
use App\Factura;
use App\Detallefactura;
use App\Pcontable;
use App\Bitacora;
use App\Ctdiario;

class FacturasController extends Controller {
    
    public function __construct()
    {
       	$this->middleware('hasAccess');    
    }
    
    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function index()
	{
        $datos = Factura::join('orgs', 'orgs.id', '=', 'facturas.org_id')
                ->select('facturas.id', 'facturas.no', 'facturas.total', 'facturas.totaldetalle', 'facturas.fecha', 'facturas.etapa', 'orgs.nombre')
                ->get();
        //dd($datos->toArray());

		foreach ($datos as $dato) {
			if ($dato->fecha) {
			  $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
			}        
		}

  		return view('contabilidad.facturas.registrar.index')->with('datos', $datos);     	
	}	

    /*************************************************************************************
     * Despliega un grupo de registros en formato de tabla
     ************************************************************************************/	
	public function pagarfacturas()
	{
        $datos = Factura::join('orgs', 'orgs.id', '=', 'facturas.org_id')
						->where('etapa', 2)
				        ->select('facturas.id', 'facturas.no', 'facturas.total', 'facturas.totalpagodetalle', 'facturas.fecha', 'facturas.etapa', 'facturas.pagada', 'orgs.nombre')
				        ->get();
        //dd($datos->toArray());

  		return view('contabilidad.facturas.pagar.index')->with('datos', $datos);     	
	}

   /*************************************************************************************
     * Despliega formulario para crear un nuevo registro
     ************************************************************************************/	
	public function create()
	{
        //Encuentra todos los proveedores registrados
		$proveedores = Org::orderBy('nombre')->get();
		$proveedores = $proveedores->lists('nombre', 'id');
		$proveedores = $proveedores->toArray();
	    //dd($proveedores);
        
        return view('contabilidad.facturas.registrar.create')
        		->with('proveedores', $proveedores);
	}     
    
    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $f_final=Carbon::today()->addDay(1);
        
        $rules = array(
            'org_id'		=> 'required',
            'no'    		=> 'Required|Numeric|digits_between:1,10|min:1',
        	'total'    		=> 'required|Numeric|min:0.01',
        	'fecha'    		=> 'required|Date|Before:' . $f_final
        );
    
        $messages = [
            'required'		=> 'Informacion requerida!',
            'before'		=> 'La fecha de la factura debe ser anterior o igual a fecha del dia de hoy!',
        	'digits_between'=> 'El numero de la factura debe tener de uno a diez digitos!',
        	'numeric'		=> 'Solo se admiten valores numericos!',
        	'date'			=> 'Fecha invalida!',
        	'min'			=> 'Se requiere un valor mayor que cero!'
        ];            	
        
        $validation = \Validator::make($input, $rules, $messages);  
		
		if ($validation->passes())
		{
			
			$dato = new Factura;
			$dato->org_id       	= Input::get('org_id');
			$dato->no			    = strtoupper(Input::get('no'));
			$dato->fecha 	       	= Input::get('fecha');
			$dato->total 	       	= Input::get('total');
			$dato->save();	
			
			// Registra en bitacoras
			$detalle =	'org_id= '.		    $dato->org_id. ', '.
						'no= '.   			$dato->no. ', '.
						'total= '.   		$dato->total. ', '.
						'fecha= '.   		$dato->fecha;
    
			Sity::RegistrarEnBitacora(1, 'facturas', $dato->id, $detalle);
			Session::flash('success', 'La factura No. ' .$dato->no. ' ha sido creada con éxito.');

			return Redirect::route('facturas.index');
		}
        return Redirect::back()->withInput()->withErrors($validation);
	}
    
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($factura_id)
	{

		$dato = Detallefactura::where('factura_id', $factura_id)->first();		
		if($dato) {
			Session::flash('warning', '<< ATENCION >> Esta factura no puede ser borrada porque tiene detalles!');
			return Redirect::route('facturas.index');
		}
		
		else {
			$dato = Factura::find($factura_id);
			$dato->delete();			

			// Registra en bitacoras
			$detalle =	'Borra el Factura '.$dato->no. ', '.
						'org_id= '.   		$dato->org_id. ', '.
						'fecha= '. 			$dato->fecha;
			
			Sity::RegistrarEnBitacora(3, 'facturas', $dato->id, $detalle);
			Session::flash('success', 'La factura No' .$dato->no. ' ha sido borrada permanentemente de la base de datos.');
			return Redirect::route('facturas.index');
		}
	}

  /****************************************************************************************
   * Esta function registra en las tablas contables los detalles de una factura ya sean gatos
   * por diversos servicios de mantenimiento o por compra de insumos
   *****************************************************************************************/
  public static function contabilizaDetallesFactura($factura_id)
  {
    //Encuentra el proveedor de la factura
    $factura= Factura::find($factura_id);
    $org_id= $factura->org_id;
    //dd($org_id);

    //determina el periodo contable vigente
    $periodo= Pcontable::where('cerrado', 0)->first();
    //dd($periodo);
    
    //Encuentra totos los detalles de un determinada factura
    $datos= Detallefactura::where('factura_id', $factura_id)
            ->join('catalogos', 'catalogos.id', '=', 'detallefacturas.catalogo_id')
            ->select('detallefacturas.precio','detallefacturas.itbms','catalogos.nombre','catalogos.id','catalogos.codigo')
            ->get();
    //dd($datos->toArray());
	
	// se anota el monto de cada uno de los gastos de la factura con su respectivo codigo de gasto
	// ctmayores
	//$fecha= Carbon::today();
	$i=1;
	foreach ($datos as $dato) {
	 	Sity::registraEnCuentas(
				$periodo->id,
				'mas', 
				6,
				$dato->id,
				$factura->fecha,
		    	$dato->nombre,
		    	($dato->precio+$dato->itbms),
		       	Null,
		       	$org_id
		       );
		
        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id  = $periodo->id;
        if ($i==1) {
        	$diario->fecha   = $factura->fecha;
        } 
        $diario->detalle = $dato->nombre;
        $diario->debito  = ($dato->precio+$dato->itbms);
        $diario->save(); 
        $i=0;
	}
	
	// se anota el total de la factura a credito incluyendo el itbms en
	// libro Mayor Auxiliar de Cuentas por Pagar
 	Sity::registraEnCuentas(
			$periodo->id,
			'mas',
			2, 
			6,
			$factura->fecha,
	    	'   Cuentas por pagar a proveedores. Factura No. '.$factura->no,
	    	$factura->total,
	       	Null,
	       	$org_id
	       );

    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo->id;
    $diario->detalle = '   Cuentas por pagar a proveedores. ';
    $diario->credito = $factura->total;
    $diario->save(); 
    
    // registra en Ctdiario principal
    $diario = new Ctdiario;
    $diario->pcontable_id  = $periodo->id;
    $diario->detalle = 'Para registrar factura No. '.$factura->no;
    $diario->save(); 

	// cambia la factura de etapa pagar			
	$factura= Factura::find($factura_id);
	$factura->etapa= 2;
	$factura->save();	
  
	// Registra en bitacoras
	$detalle =	'Contabiliza factura '.$factura_id. ', '.
				'pcontable_id= '. ', '.
				'no= '.$factura->no. ', '.
				'org_id= '.$factura->org_id. ', '.
				'fecha= '.$factura->fecha;

	Sity::RegistrarEnBitacora(15, 'facturas', $factura_id, $detalle);
	Session::flash('success', 'La factura No. ' .$factura->no. ' ha sido cotabilizada.');

	return Redirect::route('facturas.index');
  }
} 