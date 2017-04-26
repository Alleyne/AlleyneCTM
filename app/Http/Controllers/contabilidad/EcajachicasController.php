<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\library\Sity;
use Session, DB;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Ecajachica;
use App\Org;
use App\User;
use App\Dte_ecajachica;
use App\Dte_desembolso;
use App\Pcontable;
use App\Bitacora;
use App\Ctdiario;
use App\Catalogo;
use App\Cajachica;
use App\Dte_cajachica;

class EcajachicasController extends Controller
{
		
		public function __construct()
		{
				$this->middleware('hasAccess');    
		}

		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			// encuentra todos los egresos de caja chica
			$datos = Ecajachica::all();
			
			// verifica la existencia de una caja chica abierta
			$cchica = Cajachica::all()->last();
			//dd($cchica);
			
			if (!$cchica) {											// La Caja chica no existe!
				$status = 1;
				$saldoCajaChica = 0;
			
			} elseif ($cchica->cerrada == 1) {	// La Caja chica se encuentra cerrada!
				$status = 2;
				$saldoCajaChica = 0;

			}	elseif ($cchica->saldo == 0) {		// La Caja chica no tiene saldo!
				$status = 3;
				$saldoCajaChica = 0;

			} else {
				$status= 4;
				$saldoCajaChica = $cchica->saldo;	// La Caja tiene saldo!				
			}
			
			return view('contabilidad.ecajachicas.registrar.index')
						->with('status', $status)
						->with('saldoCajaChica', $saldoCajaChica)
						->with('datos', $datos);
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			//Encuentra todos los proveedores registrados
			$proveedores = Org::orderBy('nombre')->pluck('nombre', 'id')->All();
			//dd($proveedores);

			return view('contabilidad.ecajachicas.registrar.create')
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

					// encuentra le saldo actual de la caja chica
					$saldoCajaChica = Cajachica::all()->last()->saldo;
					//dd((float)$saldoCajaChica, (float)Input::get('monto'));
					
					if ((float)$saldoCajaChica < (float)Input::get('monto')) {
	          Session::flash('danger', '<< ERROR >> El monto sobre pasa el saldo actual de la caja chica de B/.'. $saldoCajaChica);
						return back()->withInput()->withErrors($validation);
					} 

					// convierte la fecha string a carbon/carbon
					$f_ecajachica = Carbon::parse(Input::get('fecha'));   
					$month= $f_ecajachica->month;    
					$year= $f_ecajachica->year;    

					// determina el periodo al que corresponde la fecha de pago    
					$pdo= Sity::getMonthName($month).'-'.$year;
				  
				  // encuentra el periodo mas antiguo abierto
					$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
				  //dd($periodo);

			    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
			    if ($pdo != $periodo->periodo) {
	          Session::flash('danger', '<< ERROR >> Solamente se permite registrar egresos de caja chica que correspondan al periodo vigente de '.$periodo->periodo);
						return back()->withInput()->withErrors($validation);
			    }

					$ecajachica = new Ecajachica;
					$ecajachica->fecha = Input::get('fecha');
					$ecajachica->org_id = Input::get('org_id');
					$ecajachica->afavorde = Org::find(Input::get('org_id'))->nombre;

					if (Input::get('tipodoc_radios') == 1) {
							$ecajachica->tipodoc = 1;
							$ecajachica->doc_no = Input::get('no');        
					
					} elseif (Input::get('tipodoc_radios') == 2) {
							$ecajachica->tipodoc = 2;
					}

					$ecajachica->descripcion = Input::get('descripcion');
					$ecajachica->total = Input::get('monto');
					$ecajachica->etapa = 1;
					$ecajachica->save();
				
					DB::commit(); 					
					Session::flash('success', 'Se registrado un nuevo egreso de caja chica!');
      
					return redirect()->route('ecajachicas.index');
				}       
		
				Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
				return back()->withInput()->withErrors($validation);

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo EcajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
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
				echo('en construccion!');
		}

		/*************************************************************************************
		 * Despliega un grupo de registros en formato de tabla
		 ************************************************************************************/  
		public function pagarecajachicas()
		{
				
			// encuentra todas las facturas que han sido contabilizadas
			$datos = Ecajachica::where('etapa', 2)->get();
					
			// formatea la fecha para cada uno de los renglones de la collection
			$datos = $datos->each(function ($dato, $key) {
					return $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
			});
			//dd($datos->toArray());

			return view('contabilidad.ecajachicas.pagar.index')->with('datos', $datos);        
		}


		/****************************************************************************************
		* Esta function registra en las tablas contables los detalles de un egreso de caja chica
		* por diversos servicios de mantenimiento o por compra de insumos
		*****************************************************************************************/
		public static function contabilizaDetallesEcajachica($ecajachica_id)
		{
			DB::beginTransaction();
			try {
			
				// verifica la existencia de una caja chica abierta
				$cchica = Cajachica::all()->last();
				//dd($cchica);
				
				if ($cchica->cerrada == 1) {
					Session::flash('danger', 'No puede contabilizar los egresos de caja chica porque no existe ninguna caja chica abierta!');
					return Redirect()->route('ecajachicas.index');
				}
				
				//Encuentra los datos generales del egreso de caja chica
				$ecajachica= Ecajachica::find($ecajachica_id);
			 
				// convierte la fecha string a carbon/carbon
				$f_ecajachica = Carbon::parse($ecajachica->fecha);   
				$month= $f_ecajachica->month;    
				$year= $f_ecajachica->year;    

				// determina el periodo al que corresponde la fecha de pago    
				$pdo= Sity::getMonthName($month).'-'.$year;

			 //Encuentra todos los detalles de un determinado egreso de caja chica
				$datos = Dte_ecajachica::where('ecajachica_id', $ecajachica_id)
																->get();
				//dd($datos->toArray());  				
			  
			  // encuentra el periodo mas antiguo abierto
				$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
			  //dd($periodo);
				
				foreach ($datos as $dato) {
						$dte_desembolso = new Dte_desembolso;
						$dte_desembolso->doc_no  				= $dato->ecajachica->doc_no;
						$dte_desembolso->serviproducto 	= $dato->nombre;
						$dte_desembolso->cantidad   		= $dato->cantidad;
						$dte_desembolso->codigo   			= $dato->codigo;
						$dte_desembolso->precio   			= $dato->precio;
						$dte_desembolso->itbms   				= $dato->itbms;
						$dte_desembolso->save(); 
				}

				//Encuentra todos los detalles de un determinado egreso de caja chica seleccionando solo el catalogo_id
				$datos = Dte_ecajachica::where('ecajachica_id', $ecajachica_id)
																->select('catalogo_id')
																->get();
				//dd($datos->toArray());              

				// encuentra cada una de las cuentas que estuvieron involucradas en el egreso de caja chica
				$cuentas = $datos->unique('catalogo_id');
				$cuentas->values()->all();
				//dd($cuentas->toArray()); 
				
				$i=1;
				$montoTotal= 0;
				$itbmsTotal= 0;               
				
				// se anota el monto de cada uno de los gastos del egreso con su respectivo codigo de gasto
				foreach ($cuentas as $cuenta) {
						$datos = Dte_ecajachica::where('ecajachica_id', $ecajachica_id)
																->where('catalogo_id', $cuenta->catalogo_id)
																->get();
						$monto= 0;
						$itbms= 0;

						// calcula los total por cada cuenta
						foreach ($datos as $dato) {
								$monto= $monto + ($dato->cantidad * $dato->precio);
								$itbms= $itbms + $dato->itbms;
						}
						//dd($cuenta->catalogo_id, $monto, $itbms); 

						// registra en libros cada un de los serviproductos encontrados en la factura de caja chica
						//$pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id=Null, $pago_id=Null, $detallepagofactura_id=Null, $org_id=Null, $ctdasm_id=Null, $anula=Null
					 
						Sity::registraEnCuentas(
							$periodo->id,
							'mas', 
							6,
							$cuenta->catalogo_id,
							$f_ecajachica,
							'Egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde,
							$monto,
							Null,
							Null,
							Null,
							$ecajachica->org_id,
							Null,
							Null
						);

						if ($itbms > 0) {
							Sity::registraEnCuentas(
								$periodo->id,
								'mas', 
								6,
								15,
								$f_ecajachica,
								'Egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde,
								$itbms,
								Null,
								Null,
								Null,
								$ecajachica->org_id,
								Null,
								Null
							);   
						}

						// registra en Ctdiario principal
						$diario = new Ctdiario;
						$diario->pcontable_id = $periodo->id;
						if ($i == 1) {
							$diario->fecha = $f_ecajachica;
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
						$montoTotal= $montoTotal + $monto;
						$itbmsTotal= $itbmsTotal + $itbms;
				}
				
				//dd($montoTotal, $itbmsTotal);

				// se anota el total del egreso de caja chica a credito incluyendo el itbms en
				// libro Mayor Auxiliar de Cuentas por Pagar a proveedores
				Sity::registraEnCuentas(
					$periodo->id,
					'mas',
					2, 
					6,
					$f_ecajachica,	
					'Egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde,
					$montoTotal + $itbmsTotal,
					Null,
					Null,
					Null,
					$ecajachica->org_id,
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
				$diario->detalle = 'Para registrar egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde;
				$diario->save(); 

				// cambia la factura de etapa pagar         
				$ecajachica= Ecajachica::find($ecajachica_id);
				$ecajachica->etapa= 3;
				$ecajachica->save();   
			
				// registra en libros el pago inmediato de la factura
				Sity::registraEnCuentas(
					$periodo->id,
					'menos',
					2, 
					6,
					$f_ecajachica,	
					'Egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde,
					$montoTotal + $itbmsTotal,
					Null,
					Null,
					Null,
					$ecajachica->org_id,
					Null,
					Null
				);

        Sity::registraEnCuentas(
          $periodo->id,
          'menos', 
          1,
          30,
					$f_ecajachica,
					'Egreso por Caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde,
					$montoTotal + $itbmsTotal,
          Null,
          Null,
          Null,
					$ecajachica->org_id,
          Null,
          Null
        );

				// registra en Ctdiario principal
				$diario = new Ctdiario;
				$diario->pcontable_id  = $periodo->id;
				$diario->fecha = $f_ecajachica;
				$diario->detalle = 'Cuentas por pagar a proveedores';
				$diario->debito = $montoTotal + $itbmsTotal;
				$diario->save(); 
				
	      $dato = new Ctdiario;
	      $dato->pcontable_id = $periodo->id;
        $dato->detalle = 'Caja chica';
        $dato->credito = $montoTotal + $itbmsTotal;
	      $dato->save();				

				// registra en Ctdiario principal
				$diario = new Ctdiario;
				$diario->pcontable_id  = $periodo->id;
				$diario->detalle = 'Para registrar egreso de caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde;
				$diario->save(); 

			  // actualiza cajachicas para que refleje nuevo saldo
				$cchica->saldo = $cchica->saldo - ($montoTotal + $itbmsTotal);
				$cchica->save();

				// agrega nuevo registro a dte_cajachicas
				$dte = new Dte_cajachica;
				$dte->fecha = $f_ecajachica;
				$dte->descripcion = 'Registra egreso de caja chica #'.$ecajachica->id.', factura #'.$ecajachica->doc_no.' - '.$ecajachica->afavorde;
				$dte->doc_no = $ecajachica->doc_no;
				$dte->disminuye = $montoTotal + $itbmsTotal;
				$dte->saldo = Dte_cajachica::all()->last()->saldo - ($montoTotal + $itbmsTotal);
				$dte->cajachica_id = $cchica->id;
				$dte->save();				

				// Registra en bitacoras
				$detalle =  'Contabiliza ecajachica_id '.$ecajachica_id. ', '.
																'pcontable_id= '.$pdo.', '.
																'doc_no= '.$ecajachica->doc_no.', '.
																'org_id= '.$ecajachica->org_id.', '.
																'fecha= '.$f_ecajachica;

   				

				// Verifica si la caja chica en estudio tiene algun desembolso sin aprobar,
				// Si no exite lo crea y le vincula los detalles del presente egreso de caja chica.
				
				// Si existe, entonces actualiza la fecha con la fecha del presente egreso de caja y
				// le vincula los detalles del presente egreso de caja chica.
				Sity::analizaDesembolsos($cchica->id);
				
				Sity::RegistrarEnBitacora(15, 'ecajachica', $ecajachica->doc_no, $detalle);
				
				DB::commit();    
				Session::flash('success', 'El egreso de caja chica No. ' .$ecajachica->doc_no. ' ha sido cotabilizado.');
				return Redirect()->route('ecajachicas.index');

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo EcajachicasController.contabilizaDetallesEcajachica, la transaccion ha sido cancelada! '.$e->getMessage());
				return back();
			}  
		}
}
