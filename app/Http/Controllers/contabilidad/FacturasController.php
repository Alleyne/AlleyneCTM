<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB, Validator;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Org;
use App\Factura;
use App\Detallefactura;
use App\Pcontable;
use App\Bitacora;
use App\Ctdiario;
use App\Catalogo;

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
    // encuentra todas las facturas que aun no han sido contabilizadas
    $datos = Factura::where('etapa','!=', 3)->get();
		
    //Encuentra todos los proveedores registrados
		$proveedores = Org::orderBy('nombre')->pluck('nombre', 'id')->All();
	  //dd($proveedores);

		// formatea la fecha
		$datos = $datos->each(function ($dato, $key) {
			return $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
		});
    //dd($datos->toArray());

		return view('contabilidad.facturas.registrar.index')
					->with('proveedores', $proveedores)
					->with('datos', $datos);     	
	}	

  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function pagarfacturas()
	{
        
    // encuentra todas las facturas que han sido contabilizadas
    $datos = Factura::where('etapa', 3)->get();
		
		// formatea la fecha para cada uno de los renglones de la collection
		$datos = $datos->each(function ($dato, $key) {
			return $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
		});
    //dd($datos->toArray());

		return view('contabilidad.facturas.pagar.index')->with('datos', $datos);     	
	}

 /*************************************************************************************
   * Despliega formulario para crear un nuevo registro
   ************************************************************************************/	
	public function create()
	{
    //Encuentra todos los proveedores registrados
		$proveedores = Org::orderBy('nombre')->pluck('nombre', 'id')->All();
	  //dd($proveedores);
        
    return view('contabilidad.facturas.registrar.create')
    			->with('proveedores', $proveedores);
	}     
    
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store()
  {
      
    DB::beginTransaction();
    try {

	      //dd(Input::all());
	      $input = Input::all();
	      $f_final=Carbon::today()->addDay(1);

        $rules = array(
            'fecha' => 'required|date',          
            'org_id' => 'required|Numeric|min:1',
            'no' => 'required|Numeric|min:1',
            'descripcion' => 'required',
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

				    // verifica que exista un periodo de acuerdo a la fecha de pago
				    $year= Carbon::parse(Input::get('fecha'))->year;
				    $month= Carbon::parse(Input::get('fecha'))->month;
				    $pdo= Sity::getMonthName($month).'-'.$year;    

					  // encuentra el periodo mas antiguo abierto
						$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
					  //dd($periodo);
					    
				    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
				    if ($pdo != $periodo->periodo) {
		          Session::flash('danger', '<< ERROR >> Solamente se permite registrar facturas de gastos que correspondan al periodo vigente de '.$periodo->periodo);
		      		return back()->withInput()->withErrors($validation);
				    }

            $factura = new Factura;
            $factura->fecha = Input::get('fecha');
            $factura->org_id = Input::get('org_id');
            $factura->afavorde = Org::find(Input::get('org_id'))->nombre;

            if (Input::get('tipodoc_radios') == 1) {
                $factura->tipodoc = 1;
                $factura->doc_no = Input::get('no');        
            
            } elseif (Input::get('tipodoc_radios') == 2) {
                $factura->tipodoc = 2;
            }

            $factura->descripcion = Input::get('descripcion');
            $factura->total = Input::get('monto');
            $factura->etapa = 1;
            $factura->save();
						
						Sity::RegistrarEnBitacora($factura, Input::get(), 'Factura', 'Registra factura de egreso de Caja general');
					
						Session::flash('success', 'La factura No. ' .$factura->doc_no. ' ha sido creada con éxito.');
            DB::commit();       

            return redirect()->route('facturas.index');
        }       
    
        Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
        return back()->withInput()->withErrors($validation);

    } catch (\Exception $e) {
        DB::rollback();
        Session::flash('warning', ' Ocurrio un error en el modulo FacturasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
        return back()->withInput();
    }
  }

  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/	
	public function destroy($factura_id)
	{

		DB::beginTransaction();
		try {
			$dato = Detallefactura::where('factura_id', $factura_id)->first();		
			
			if($dato) {
				Session::flash('warning', '<< ATENCION >> Esta factura no puede ser borrada porque tiene detalles!');
				return Redirect()->route('facturas.index');
			}
			
			else {
				$dato = Factura::find($factura_id);
				$dato->delete();			

				// Registra en bitacoras
				Sity::RegistrarEnBitacora($dato, Null, 'Factura', 'Elimina factura de egreso de Caja general');   
				
				Session::flash('success', 'La factura No' .$dato->doc_no. ' ha sido borrada permanentemente de la base de datos.');
				DB::commit();				
				return Redirect()->route('facturas.index');
			}

		} catch (\Exception $e) {
	    DB::rollback();
    	Session::flash('warning', ' Ocurrio un error en el modulo FacturasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());
    	return back();
		}		
	}

  /****************************************************************************************
   * Esta function registra en las tablas contables los detalles de una factura ya sean gatos
   * por diversos servicios de mantenimiento o por compra de insumos
   *****************************************************************************************/
  public static function contabilizaDetallesFactura($factura_id)
  {
		DB::beginTransaction();
		try {
	    
	    //Encuentra el proveedor de la factura
	    $factura= Factura::find($factura_id);
	    $org_id= $factura->org_id;
	    //dd($org_id);

	    // convierte la fecha string a carbon/carbon
	    $f_factura = Carbon::parse($factura->fecha);   
	    $month= $f_factura->month;    
	    $year= $f_factura->year;    

	    // determina el periodo al que corresponde la fecha de pago    
	    $pdo= Sity::getMonthName($month).'-'.$year;
	    $periodo= Pcontable::where('periodo', $pdo)
												->where('cerrado', 0)
												->first();
	    //dd($periodo); 

	    if (!$periodo) {
        Session::flash('warning', '<< ATENCION >> La presente factura no puede ser contabilizada ya que el periodo contable al cual pertenece ha sido cerrado. Borre la factura y sus detalles e ingrecela nuevamente con fecha del periodo actualmente abierto.');
        return back();
	    }

	    //Encuentra todos los detalles de una determinada factura
      $datos = Detallefactura::where('factura_id', $factura_id)
                              ->select('catalogo_id')
                              ->get();
      //dd($datos->toArray());             

      // encuentra cada una de las cuentas que estuvieron involucradas en la factura
      $cuentas = $datos->unique('catalogo_id');
      $cuentas->values()->all();
      //dd($cuentas->toArray()); 
      
      $i=1;
      $montoTotal= 0;
      $itbmsTotal= 0;               
      
      // se anota el monto de cada uno de los gastos del egreso con su respectivo codigo de gasto
      foreach ($cuentas as $cuenta) {
        $datos = Detallefactura::where('factura_id', $factura_id)
                            ->where('catalogo_id', $cuenta->catalogo_id)
                            ->get();
        $monto= 0;
        $itbms= 0;

        // calcula los total por cada cuenta
        foreach ($datos as $dato) {
            $monto= $monto + ($dato->cantidad * $dato->precio);
            $itbms= $itbms + $dato->itbms;
        }
	
			 	Sity::registraEnCuentas(
						$periodo->id,
						'mas', 
						6,
						$cuenta->catalogo_id,
						$f_factura,
						'Egreso por Caja general, factura #'.$factura->doc_no.' - '.$factura->afavorde,
						$monto,
						Null,
						Null,
						Null,
						$factura->org_id,
						Null,
						Null
					);
				
        if ($itbms > 0) {
				 	Sity::registraEnCuentas(
						$periodo->id,
						'mas', 
						6,
						15,
						$f_factura,
						'Egreso por Caja general, factura #'.$factura->doc_no.' - '.$factura->afavorde,
						$itbms,
						Null,
						Null,
						Null,
						$factura->org_id,
						Null,
						Null
					);	        
				}
	        
        // registra en Ctdiario principal
        $diario = new Ctdiario;
        $diario->pcontable_id = $periodo->id;
        if ($i == 1) {
        	$diario->fecha = $f_factura;
        	$i = 0;	        
        } 
        
        $diario->detalle = $dato->cuenta;
        $diario->debito  = $monto;
        $diario->save(); 

        if ($itbms > 0) {
	        $diario = new Ctdiario;
	        $diario->pcontable_id  = $periodo->id;
	        $diario->detalle = Catalogo::find(15)->nombre;
	        $diario->debito  = $itbms;
	        $diario->save(); 
				}
        
        //acumula los totales finales
        $montoTotal = $montoTotal + $monto;
        $itbmsTotal = $itbmsTotal + $itbms;
			}
			
			// se anota el total de la factura a credito incluyendo el itbms en
			// libro Mayor Auxiliar de Cuentas por Pagar
		 	Sity::registraEnCuentas(
				$periodo->id,
				'mas',
				2, 
				6,
				$f_factura,
				'Egreso por Caja general, factura #'.$factura->doc_no.' - '.$factura->afavorde,
				$montoTotal + $itbmsTotal,
				Null,
				Null,
				Null,
				$factura->org_id,
				Null,
				Null
			);

	    // registra en Ctdiario principal
	    $diario = new Ctdiario;
	    $diario->pcontable_id  = $periodo->id;
	    $diario->detalle = Catalogo::find(6)->nombre;
	    $diario->credito = $montoTotal + $itbmsTotal;
	    $diario->save(); 
	    
	    // registra en Ctdiario principal
	    $diario = new Ctdiario;
	    $diario->pcontable_id  = $periodo->id;
	    $diario->detalle = 'Para registrar egreso por Caja general, factura #'.$factura->doc_no.' - '.$factura->afavorde;
	    $diario->save(); 

			// cambia la factura de etapa pagar			
			$factura = Factura::find($factura_id);
			$factura->etapa = 3;
			$factura->save();	
		  
		  // Registra en bitacoras
  		Sity::RegistrarEnBitacora($factura, Null, 'Factura', 'Contabiliza factura de egreso de Caja general'); 
			
			DB::commit();		
			
			Session::flash('success', 'La factura No. ' .$factura->doc_no. ' ha sido cotabilizada.');
			return Redirect()->route('facturas.index');

		} catch (\Exception $e) {
		  DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo FacturasController.contabilizaDetallesFactura, la transaccion ha sido cancelada! '.$e->getMessage());
			return back()->withInput();
		}  
	}
} 