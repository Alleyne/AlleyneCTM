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
use App\Pcontable;
use App\Bitacora;
use App\Ctdiario;
use App\Catalogo;

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
			$datos = Ecajachica::all();
			return view('contabilidad.ecajachicas.registrar.index')->withDatos($datos);
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

					Session::flash('success', 'Se registrado un nuevo egreso de caja chica!');
					DB::commit();       
					return redirect()->route('ecajachicas.index');
				}       
		
				Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
				return back()->withInput()->withErrors($validation);

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo EcajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());
				return back()->withInput()->withErrors($validation);
			}
		}

		/**
		 * Display the specified resource.
		 *
		 * @param  int  $id
		 * @return \Illuminate\Http\Response
		 */
		public function show($id)
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
				//
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
			
				//Encuentra los datos generales del egreso de caja chica
				$ecajachica= Ecajachica::find($ecajachica_id);
			 
				// convierte la fecha string a carbon/carbon
				$f_ecajachica = Carbon::parse($ecajachica->fecha);   
				$month= $f_ecajachica->month;    
				$year= $f_ecajachica->year;    

				// determina el periodo al que corresponde la fecha de pago    
				$pdo= Sity::getMonthName($month).'-'.$year;
				$periodo= Pcontable::where('periodo', $pdo)
														->where('cerrado', 0)
														->first();
				//dd($periodo); 

				if (!$periodo) {
						Session::flash('warning', '<< ATENCION >> El presente egreso de caja chica no puede ser contabilizado ya que el periodo contable al cual pertenece ha sido cerrado. Borre el egreso de caja chica y sus detalles e ingrecela nuevamente con fecha del periodo actualmente abierto.');
						return back();
				}

				//Encuentra todos los detalles de un determinado egreso de caja chica
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
						//$pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id=Null, $pago_id=Null, $detallepagofactura_id=Null, $org_id=Null, $ctdasm_id=Null, $anula=Null
					 
						Sity::registraEnCuentas(
							$periodo->id,
							'mas', 
							6,
							$cuenta->catalogo_id,
							$f_ecajachica,
							$dato->cuenta,
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
								Catalogo::find(15)->nombre.', egreso de caja chica No. '.$ecajachica->doc_no.', proveedor No. '.$ecajachica->org_id,
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
					'   Cuentas por pagar a proveedores. Egreso de caja chica No. '.$ecajachica->doc_no.', Proveedor No. '.$ecajachica->org_id,
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
				$diario->detalle = '   Cuentas por pagar a proveedores. '.$ecajachica->org_id;
				$diario->credito = $montoTotal + $itbmsTotal;
				$diario->save(); 
				
				// registra en Ctdiario principal
				$diario = new Ctdiario;
				$diario->pcontable_id  = $periodo->id;
				$diario->detalle = 'Para registrar egreso de caja chica No. '.$ecajachica->doc_no;
				$diario->save(); 

				// cambia la factura de etapa pagar         
				$ecajachica= Ecajachica::find($ecajachica_id);
				$ecajachica->etapa= 3;
				$ecajachica->save();   
			
				// Registra en bitacoras
				$detalle =  'Contabiliza ecajachica_id '.$ecajachica_id. ', '.
																'pcontable_id= '.$pdo.', '.
																'doc_no= '.$ecajachica->doc_no.', '.
																'org_id= '.$ecajachica->org_id.', '.
																'fecha= '.$f_ecajachica;

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
