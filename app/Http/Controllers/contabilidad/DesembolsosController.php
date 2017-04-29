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
		public function verDesembolsos($cajachica_id) {

			//dd($cajachica_id);
			// encuentra todos los desembolsos que pertenecen a una determinada caja chica
			$datos = Desembolso::where('cajachica_id', $cajachica_id)->get();
			
			return view('contabilidad.desembolsos.verDesembolsos')
						->with('cajachica_id', $cajachica_id)
						->with('datos', $datos);
		}

		/**
		 * Display the specified resource.
		 *
		 * @param  int  $id
		 * @return \Illuminate\Http\Response
		 */
		public function show($desembolso_id) {

	    // encuentra los detalles del desembolso
	    $datos = Dte_desembolso::where('desembolso_id', $desembolso_id)->get();
	    //dd($ecajachica_id, $datos->toArray());		

	    // encuentra las generales del desembolso
	    $desembolso = Desembolso::find($desembolso_id);
	    //dd($desembolso);

	    // encuentrea las generales de la cajachica a la que pertenece el desembolso
	    $cchica = $desembolso->cajachica;
	    //dd($cchica);
  		
  		// calcula y agrega el total
			$i = 0;		
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
					 ->with('cchica', $cchica)
					 ->with('f_actual', Date::parse(Carbon::today())->toFormattedDateString())
					 ->with('datos', $datos);
		}
		
		/**
		 * Display the specified resource.
		 *
		 * @param  int  $id
		 * @return \Illuminate\Http\Response
		 */
		public function aprobarInforme($desembolso_id, $cchica_id) {

			// encuentra los datos del desembolso
			//$cchica_id= Desembolso::find($desembolso_id)->$desembolso->cajachica->id;
	    
	    // encuentrea las generales de la cajachica a la que pertenece el desembolso
	    //$cchica = $desembolso->cajachica;
	    //dd($cchica);

	    //Encuentra todos los proveedores registrados
			$aprobadores = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
		  //dd($aprobadores);

			return view('contabilidad.desembolsos.aprobarInforme')
						->with('aprobadores', $aprobadores)
					  ->with('cchica_id', $cchica_id)
						->with('desembolso_id', $desembolso_id);
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Response
		 */
		public function storeAprobarInforme()	{
				
			DB::beginTransaction();
			try {

				//dd(Input::all());
				$input = Input::all();

				$rules = array(
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
					$dte_desembolsos = Dte_desembolso::where('desembolso_id', Input::get('desembolso_id'))->get();
					//dd($dte_desembolsos->toArray());

					$montoTotal= 0;
					foreach ($dte_desembolsos as $dte_desembolso) {
						$montoTotal= $montoTotal + (($dte_desembolso->cantidad * $dte_desembolso->precio) + $dte_desembolso->itbms);
					}
					//dd($montoTotal, (float)Input::get('monto'));

					if ($montoTotal != (float)Input::get('monto')) {
						Session::flash('danger', '<< ATENCION >> El monto del cheque debera se igual al monto total del informe de desembolso!');
						return back()->withInput()->withErrors($validation);
					}

  				// salva la informacion
					$desembolso = Desembolso::find(Input::get('desembolso_id'));

					$desembolso->fecha = Carbon::today();
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
      		$dato->fecha = Carbon::today();
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
	        $dato->detalle = 'Para registrar el chq #'.Input::get('cheque').' y reponer el fondo de caja chica #'.$desembolso->cajachica->id;
		      $dato->save(); 
		      
	        Sity::registraEnCuentas(
	          $periodo->id,
	          'mas', 
	          1,
	          30,
	          Carbon::today(),
	          'Para reponer fondo de caja chica #'.$desembolso->cajachica->id.', chq #'.Input::get('cheque'),
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
	          Carbon::today(),
	          'Para reponer fondo de caja chica #'.$desembolso->cajachica->id.', chq #'.Input::get('cheque'),
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

		      // registra nuevo detalle en dte_cajachicas
		      $dte_cajachica = new Dte_cajachica;
		      $dte_cajachica->fecha = Carbon::today();
		      $dte_cajachica->descripcion = 'Para reponer fondo de caja chica #'.$desembolso->cajachica->id.', chq #'.Input::get('cheque');
		      $dte_cajachica->doc_no = Input::get('cheque');
		      $dte_cajachica->aumenta = Input::get('monto');
		      $dte_cajachica->saldo = Input::get('monto') + $montoActual;
		      $dte_cajachica->aprueba_id = Input::get('user_id');
		      $dte_cajachica->aprueba = User::find(Input::get('user_id'))->nombre_completo;
		      $dte_cajachica->cajachica_id = $desembolso->cajachica->id;
		      $dte_cajachica->save();   
		     
		      // Actualiza el saldo de cajachicas
		      $cchica = Cajachica::find($desembolso->cajachica->id);
		      $cchica->saldo = Input::get('monto') + $montoActual;
		      $cchica->save();

					Session::flash('success', 'Se ha registrado la aprobacion de informe de caja chica #'.$desembolso->cajachica->id);
					DB::commit();       

					return redirect()->route('verDesembolsos', $desembolso->cajachica->id);
				}       
		
				Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
				return back()->withInput()->withErrors($validation);

			} catch (\Exception $e) {
				DB::rollback();
				Session::flash('warning', ' Ocurrio un error en el modulo DesembolsosController.storeAprobarInforme, la transaccion ha sido cancelada! '.$e->getMessage());
				return back();
			}
		}
}
