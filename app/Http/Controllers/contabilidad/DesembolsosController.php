<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\library\Sity;
use Session, DB;
use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\Desembolso;
use App\Dte_desembolso;
use App\Bitacora;
use App\Dte_ecajachica;
use App\User;
use App\Pcontable;
use App\Ctdiario;
use App\Catalogo;
use App\Cajachica;
use App\Dte_cajachica;

class DesembolsosController extends Controller
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
		public function verDesembolsos($cajachica_id)
		{
			$cchicaCerrada= Cajachica::find($cajachica_id)->cerrada;

			$datos = Desembolso::where('cajachica_id', $cajachica_id)->get();
			return view('contabilidad.desembolsos.verDesembolsos')
						->with('cchicaCerrada', $cchicaCerrada)
						->with('datos', $datos);
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			return view('contabilidad.desembolsos.create');
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
						'fecha' => 'required|date'          
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

					// convierte la fecha string a carbon/carbon
					$f_desembolso = Carbon::parse(Input::get('fecha'));   
					$month= $f_desembolso->month;    
					$year= $f_desembolso->year;    

					// determina el periodo al que corresponde la fecha de pago    
					$pdo= Sity::getMonthName($month).'-'.$year;
				  
				  // encuentra el periodo mas antiguo abierto
					$periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
				  //dd($periodo);

			    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
			    if ($pdo != $periodo->periodo) {
	          Session::flash('danger', '<< ERROR >> Solamente se permite crear informes de caja chica que correspondan al periodo vigente de '.$periodo->periodo);
						return back()->withInput()->withErrors($validation);
			    }

					//verifica si hay detalles de desembolsos por asignar
					$dte_desembolsos= Dte_desembolso::where('desembolso_id', 0)->first();
					//dd($dte_desembolsos);

					if ($dte_desembolsos) {
						$desembolso = new Desembolso;
						$desembolso->fecha = Input::get('fecha');
						$desembolso->cajachica_id = Cajachica::all()->last()->id;
						$desembolso->save();

						//encuentra todos los detalles de desembolso que tengan desembolso_id igual a cero y se los asigna
						// al recien creado desembolso
						Dte_desembolso::where('desembolso_id', 0)
						          ->update(['desembolso_id' => $desembolso->id]);

						Session::flash('success', 'Se registrado un nuevo desembolso de caja chica!');
						DB::commit();       
						return redirect()->route('desembolsos.index');
					
					} else {
						Session::flash('danger', '<< ATENCION >> No se puede crear el desembolso ya que no existe ningun servicio o producto pagados por caja chica!');
						return redirect()->route('desembolsos.index');
					}
				}       
		
				Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
				return back()->withInput()->withErrors($validation);

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo DesembolsosController.store, la transaccion ha sido cancelada! '.$e->getMessage());
				return back()->withInput()->withErrors($validation);
			}
		}

		/**
		 * Display the specified resource.
		 *
		 * @param  int  $id
		 * @return \Illuminate\Http\Response
		 */
		public function show($desembolso_id)
		{
	    
	    $datos = Dte_desembolso::where('desembolso_id', $desembolso_id)
	            ->get();
	    //dd($ecajachica_id, $datos->toArray());		

  		// calcula y agrega el total
			$i=0;		
			$subTotal = 0;
			$totalItbms = 0;

			foreach ($datos as $dato) {
			    $datos[$i]["total"] = number_format((($dato->cantidad * $dato->precio) + $dato->itbms),2);
			    $subTotal = $subTotal + ($dato->cantidad * $dato->precio);
			    $totalItbms = $totalItbms + $dato->itbms;		    
			    $i++;
			}        
	    
	    //dd($datos->toArray());	
			
			return view('contabilidad.desembolsos.show')
					 ->with('subTotal', $subTotal)
					 ->with('totalItbms', $totalItbms)
					 ->with('datos', $datos);
		}

		public function aprobarInforme($desembolso_id)
		{
			// encuentra los datos del desembolso
			//$datos= Desembolso::find($desembolso_id);
	    
	    //Encuentra todos los proveedores registrados
			$aprobadores = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
		  //dd($aprobadores);

			return view('contabilidad.desembolsos.aprobarInforme')
						->with('aprobadores', $aprobadores)
						->with('desembolso_id', $desembolso_id);
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Response
		 */
		public function storeAprobarInforme()
		{
				
			DB::beginTransaction();
			try {

				//dd(Input::all());
				$input = Input::all();

				$rules = array(
					'fecha' => 'required|date',          
					'user_id' => 'required|Numeric|min:1',    
					'cheque' => 'required',      
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

					// verifica que el monto total del desembolso sea igual al monto del cheque de aprobacion
					$dte_desembolsos= Dte_desembolso::where('desembolso_id', Input::get('desembolso_id'))->get();
					//dd($dte_desembolsos->toArray());

					$montoTotal= 0;
					foreach ($dte_desembolsos as $dte_desembolso) {
						$montoTotal= $montoTotal + ($dte_desembolso->precio + $dte_desembolso->itbms);
					}
					//dd($montoTotal, (float)Input::get('monto'));

					if ($montoTotal != (float)Input::get('monto')) {
						Session::flash('danger', '<< ATENCION >> El monto del cheque debera se igual al monto total del informe de desembolso!');
						return back()->withInput()->withErrors($validation);
					}

  				// salva la informacion
					$desembolso = Desembolso::find(Input::get('desembolso_id'));
					
					//dd(Carbon::parse($desembolso->fecha), Carbon::parse(Input::get('fecha')));

					// antes de salvar verifica que la fecha de aprobacion sea igual o posterior a la fecha del desembolso
					if (Carbon::parse($desembolso->fecha)->gt(Carbon::parse(Input::get('fecha')))) {
						Session::flash('danger', '<< ATENCION >> La fecha de aprobacion debera ser igual o posterior a la fecha del registro de desembolsol!');
						return back()->withInput()->withErrors($validation);
					}

					$desembolso->fecha = Input::get('fecha');
					$desembolso->cheque = Input::get('cheque');
					$desembolso->monto = Input::get('monto');
					$desembolso->aprobadopor_id = Input::get('user_id');
					$desembolso->aprobadopor = User::find(Input::get('user_id'))->nombre_completo;
					$desembolso->aprobado = 1;
					$desembolso->save();

					// encuentra el periodo mas antiguo abierto
		      $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
		      //dd($periodo);

					// registra en Ctdiario principal
		      $dato = new Ctdiario;
		      $dato->pcontable_id = $periodo->id;
      		$dato->fecha = Input::get('fecha');
	        $dato->detalle = 'Caja chica';
	        $dato->debito = Input::get('monto');
		      $dato->save();
	        
		      $dato = new Ctdiario;
		      $dato->pcontable_id = $periodo->id;
	        $dato->detalle = Catalogo::find(8)->nombre;
	        $dato->credito = Input::get('monto');
		      $dato->save(); 

		      // registra en Ctdiario principal
		      $dato = new Ctdiario;
		      $dato->pcontable_id = $periodo->id;
	        $dato->detalle = 'Para registrar el cheque No. '.Input::get('cheque').' y reponer el fondo de caja chica';
		      $dato->save(); 
		      
	        Sity::registraEnCuentas(
	          $periodo->id,
	          'mas', 
	          1,
	          30,
	          Input::get('fecha'),
	          'Caja chica',
	          Input::get('monto'),
	          Null,
	          Null,
	          Null,
	          Null,
	          Null,
	          Null
	        );

	        Sity::registraEnCuentas(
	          $periodo->id,
	          'menos', 
	          1,
	          8,
	          Input::get('fecha'),
	          Catalogo::find(8)->nombre,
	          Input::get('monto'),
	          Null,
	          Null,
	          Null,
	          Null,
	          Null,
	          Null
	        );
					
		      // calcula el saldo actual de los detalles de cajachicas
		      $montoActual = Dte_cajachica::all()->last();
		      if ($montoActual) {
		        $montoActual= $montoActual->saldo;
		      } else {
		        $montoActual= 0;
		      }
		      //dd($montoActual);

		      // encuentra los datos de la ultima caja chica
		      $cchica = Cajachica::all()->last();

		      // registra nuevo detalle en dte_cajachicas
		      $dte_cajachica = new Dte_cajachica;
		      $dte_cajachica->fecha = Input::get('fecha');
		      $dte_cajachica->descripcion = 'Reponer fondo de caja chica, cheque no. '.Input::get('cheque');
		      $dte_cajachica->doc_no = Input::get('cheque');
		      $dte_cajachica->aumenta = Input::get('monto');
		      $dte_cajachica->saldo = Input::get('monto') + $montoActual;
		      $dte_cajachica->aprueba_id = Input::get('user_id');
		      $dte_cajachica->aprueba = User::find(Input::get('user_id'))->nombre_completo;
		      $dte_cajachica->cajachica_id = $cchica->id;
		      $dte_cajachica->save();   
		     
		      // Actualiza el saldo de cajachicas
		      $cchica->saldo = Input::get('monto') + $montoActual;
		      $cchica->save();

					// actualiza del diario de caja chica para que refleje nuevo saldo
					$cajachica = new Cajachica;
					$cajachica->saldo = Cajachica::all()->last()->saldo - Input::get('monto');
					$cajachica->save();

					Session::flash('success', 'Se ha registrado la aprobacion de informe de caja chica!');
					DB::commit();       
					return redirect()->route('desembolsos.index');
				}       
		
				Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
				return back()->withInput()->withErrors($validation);

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo DesembolsosController.storeAprobarInforme, la transaccion ha sido cancelada! '.$e->getMessage());
				return back()->withInput()->withErrors($validation);
			}
		}
}
