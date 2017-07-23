<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\library\Npagonoid;
use App\library\Npago;
use App\library\Sity;
use Session, DB;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Auth;

use App\Bitacora;
use App\User;
use App\Pcontable;
use App\Ctdiario;
use App\Catalogo;
use App\Pagosnoid;
use App\Banco;
use App\Un;
use App\Pago;

class PagosnoidsController extends Controller
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
		
		$datos = Pagosnoid::all();
		//dd($datos->toArray());

		return view('contabilidad.pagosnoids.index')
					->withDatos($datos);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
    // obtiene todas las instituciones bancarias actualmente registrada
    $bancos= Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
		//dd($bancos);

		return view('contabilidad.pagosnoids.create')
						->with('bancos', $bancos);
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
				'banco_id' => 'required',
				'doc_no' => 'required',
				'monto' => 'required'
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
				$fecha = Carbon::parse(Input::get('fecha'));   
				$month = $fecha->month;    
				$year = $fecha->year;    

				// determina el periodo al que corresponde la fecha de pago    
				$pdo = Sity::getMonthName($month).'-'.$year;
			  
			  // encuentra el periodo mas antiguo abierto
				$periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
			  //dd($periodo);

		    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
		    //if ($pdo != $periodo->periodo) {
         // Session::flash('danger', '<< ERROR >> Solamente se permite registrar pagos no identificados que correspondan al periodo vigente de '.$periodo->periodo);
					//return back()->withInput()->withErrors($validation);
		    //}

	      // incializa variables a utilizar
	      $cuenta_31 = Catalogo::find(31)->nombre;    // 2030.00 Pagos no indentificados
	      $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional

				$pagosnoid = new Pagosnoid;
				$pagosnoid->f_pago = Input::get('fecha');
				$pagosnoid->banco_id = Input::get('banco_id');
				$pagosnoid->banco = Banco::find(Input::get('banco_id'))->nombre;
				
				/*if (Input::get('tipodoc_radios') == 1) {
					$pagosnoid->tipo = 1;
				
				} elseif (Input::get('tipodoc_radios') == 2) {
					$pagosnoid->tipo = 2;
				}*/
				
				$pagosnoid->doc_no = Input::get('doc_no');  
				$pagosnoid->monto = Input::get('monto');
				$pagosnoid->save();
			
  			// cambia el formato de la fecha del pago no identificado
  			$fecha = Date::parse($pagosnoid->f_pago)->toFormattedDateString();

  			Sity::RegistrarEnBitacora($pagosnoid, Input::get(), 'Pagosnoid', 'Registra pago no identificado');

				// registra en libros
				// registra en Ctdiario principal
	      $dato = new Ctdiario;
	      $dato->pcontable_id = $periodo->id;
    		$dato->fecha = Input::get('fecha');
        $dato->detalle = $cuenta_8;        
        $dato->debito = Input::get('monto');
	      $dato->save();
        
	      $dato = new Ctdiario;  //31 2030.00 Pagos no indentificados
	      $dato->pcontable_id = $periodo->id;
        $dato->detalle = $cuenta_31;
        $dato->credito = Input::get('monto');
	      $dato->save(); 

	      // registra en Ctdiario principal
	      $dato = new Ctdiario;
	      $dato->pcontable_id = $periodo->id;	
        $dato->detalle = 'Para registrar pago no identificado #'.$pagosnoid->id.' '.$fecha;
	      $dato->save(); 
	      
        Sity::registraEnCuentas(
          $periodo->id,
          'mas', 
          1,
          8,
          Input::get('fecha'),
          $cuenta_31.' #'.$pagosnoid->id.' '.$fecha,
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
          'mas', 
          2,
          31,
          Input::get('fecha'),
          $cuenta_31.' #'.$pagosnoid->id.' '.$fecha,
          Input::get('monto'),
          Null,
          Null,
          Null,
          Null,
          Null,
          Null
        );

				Session::flash('success', 'Se registrado un nuevo pago no identificado!');
				DB::commit();       
				return redirect()->route('pagosnoids.index');
			}       
	
			Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo PagosnoidsController.store, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}

	public function identificarPagoCreate($pagosnoid_id)
	{

    // obtiene todas las unidades
    $uns= Un::orderBy('codigo')->pluck('codigo', 'id')->all();
		//dd($uns);

		return view('contabilidad.pagosnoids.identificarPagoCreate')
						->with('pagosnoid_id', $pagosnoid_id)
						->with('uns', $uns);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function identificarPagoStore()
	{
			
		DB::beginTransaction();
		try {

			//dd(Input::all());
			$input = Input::all();

			$rules = array(
       'un_id' => 'required'
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
				//encuentra los datos de la unidad
				$uns = Un::find(Input::get('un_id'));
				
				$propietarios="";
				foreach ($uns->props as $prop) {
				    $propietarios= $propietarios.', '. $prop->user->nombre_completo;
				}
				//dd($trimmed = ltrim($propietarios, " , "));
				
				$pagosnoid = Pagosnoid::find(Input::get('pagosnoid_id'));
				$pagosnoid->un_id = $uns->id;
				$pagosnoid->codigo = $uns->codigo;
				$pagosnoid->propietarios = ltrim($propietarios, " , "); 
				$pagosnoid->identificado = 1;
				$pagosnoid->save();															
  			
  			Sity::RegistrarEnBitacora($pagosnoid, Input::get(), 'Pagosnoid', 'Identifica pago no identificado');			
				
				Session::flash('success', 'Se identifico el pago no identificado!');
				DB::commit();       
				return redirect()->route('pagosnoids.index');
			}       
	
			Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);

		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo PagosnoidsController.indentificarPagosStore, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}

  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function contabilizaPagonoid($pagonoid_id) {
		//dd($pagosnoid_id, $f_pago, $un_id, $monto, $banco_id, $doc_no);
		DB::beginTransaction();
		try {
		  // encuentra los detalles del pago ya identificado
	    $pagoid = Pagosnoid::find($pagonoid_id);
	    
	    // calcula el periodo al que corresponde la fecha de pago
	    //$f_pago= Carbon::parse($f_pago);
	    //$f_pago= Carbon::today();
	    //$year= $f_pago->year;
	    //$month= $f_pago->month;
	    //$pdo= Sity::getMonthName($month).'-'.$year;    
		    
		  // encuentra el periodo mas antiguo abierto
			$periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
	    //dd($pdo, $periodo->periodo);
	    
	    // solamente se permite registrar pagos que correspondan al periodo mas antiguo abierto
	    //if ($pdo != $periodo->periodo) {
        //Session::flash('danger', '<< ERROR >> Solamente se permite registrar pagos que correspondan al periodo de '.$periodo->periodo);
    		//return back()->withInput()->withErrors($validation);
	   //}

			// antes de iniciar el proceso de pago, ejecuta el proceso de penalizacion
			//Npago::penalizarTipo2(Carbon::today(), $pagoid->un_id, $periodo->id);

			// Almacena el monto de la transaccion
			$montoRecibido = round(floatval($pagoid->monto),2);

			// Registra el pago recibido
			$dato = new Pago;
			$dato->banco_id    = $pagoid->banco_id;
			$dato->trantipo_id = 4;
		  $dato->trans_no    = $pagoid->doc_no; 
			$dato->monto       = $montoRecibido;
			$dato->f_pago      = Carbon::today();
			$dato->descripcion = 'Para registrar pago no identificado';
		  $dato->fecha 	   	 = Carbon::today(); 		    
			$dato->entransito  = 0;
			$dato->un_id       = $pagoid->un_id;
	    $dato->user_id 	   = Auth::user()->id; 		    
	    $dato->save();
			
			// Registra en bitacoras
  		Sity::RegistrarEnBitacora($dato, Null, 'Pago', 'Contabiliza pago identificado');
			
			// actualiza pago no identificado como contabilizado
			$dto = Pagosnoid::find($pagonoid_id);
			$dto->contabilizado = 1;
	    $dto->save();

			// proceso de contabilizar el pago recibido
			Npagonoid::iniciaPago($dato, $periodo);

			DB::commit();		            
      Session::flash('success', 'El pago identificado ha sido creado y procesado con éxito.');
			return redirect()->route('pagosnoids.index');
	
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', 'Ocurrio un error en el modulo PagosController.contabilizaPagonoid, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}


}