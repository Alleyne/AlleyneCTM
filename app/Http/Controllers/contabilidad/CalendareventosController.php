<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Jenssegers\Date\Date;
use App\library\Sity;
use App\library\Npago;
use Carbon\Carbon, Log, DB, Session, Auth;

use App\Calendarevento;
use App\Am_alquilere;
use App\Un;
use App\Prop;
use App\User;
use App\Trantipo;
use App\Pago;
use App\Banco;
use App\Eventodevolucione;
use App\Pcontable;
use App\Catalogo;

class CalendareventosController extends Controller
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
 
      // revisa el status de cada evento y si encuentra alguno ya culminado le actualiza el status
      Calendarevento::where('end', '<', Carbon::now())
                    ->where('status', 2)
                    ->update(['status' => 3, 'className' => 'bg-color-green txt-color-white', 'icon' => 'fa-lock']);

      $datos = Calendarevento::all();

      // formatea los datos de la colleccion
      $datos->map(function ($datos) {
        $datos->start = Date::parse($datos->start)->format('M j, Y g:i A');
        $datos->end = Date::parse($datos->end)->format('M j, Y g:i A');
        $datos->am_id = Am_alquilere::find($datos->am_id)->codigo;
        $datos->un_id = Un::find($datos->un_id)->codigo;       
        $props = User::find($datos->user_id);
        
        // agrega un nuevo elemento a la collection
        $datos->props = $props->cedula.' '.$props->nombre_completo;
      });
      // dd($datos);

      //Encuentra todos los paquetes de amenidades alquilables
      $ams = Am_alquilere::orderBy('nombre')->pluck('nombre', 'id')->All();
      //dd($ams);

      // obtiene todas las instituciones bancarias actualmente registrada
      $bancos = Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
      //dd($bancos);

      // encuentra todas las unidades que tienen por lo menos un propietario
      $uns = Un::orderBy('codigo')->has('props')->get();
      //dd($uns);

      $uns->map(function ($uns) {
        
        // encuentra el o los propietarios de la unidad
        $props = Prop::where('un_id', $uns->id)
                 ->join('users','users.id','=','props.user_id')
                 ->select('cedula', 'cedula', 'nombre_completo')
                 ->get();
        
        $propietarios = "";
        foreach ($props as $prop) {
          $propietarios = $propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;         
        }
      
        $uns->props = $uns->codigo.' '.$propietarios;
      });
      //dd($uns);

      // obtiene todas las unidades
      $uns = $uns->pluck('props', 'id')->all();
      //dd($uns);

      // obtiene todos los diferentes tipos de pagos
      $trantipos= Trantipo::pluck('nombre', 'id')->all();
      $trantipos= Trantipo::orderBy('nombre')->get();   
      //dd($trantipos); 

      return view('contabilidad.calendareventos.index')
                ->with('ams', $ams)
                ->with('uns', $uns)
                ->with('bancos', $bancos)
                ->with('trantipos', $trantipos)
                ->with('datos', $datos); 
    }

    public function cargaEventos()
    {
      
      // revisa el status de cada evento y si encuentra alguno ya culminado le actualiza el status
      Calendarevento::where('end', '<', Carbon::now())
                    ->where('status', 2)
                    ->update(['status' => 3, 'className' => 'bg-color-green txt-color-white', 'icon' => 'fa-lock']);

      $data = Calendarevento::where('status','<=',4)->get(['id','title','un_id','description','start','end','allDay','className','icon','status']);
 
      $data->map(function ($data) {
        $data->un_id = Un::find($data->un_id)->codigo;       
        //$props = User::find($data->user_id);
        
        // agrega el nuevo elemento a la collection
        //$data->props = $props->cedula.' '.$props->nombre_completo;
      });
      //dd($data);

      return Response()->json($data);       

    }

    public function verCalendario()
    {
 
      $datos = Calendarevento::all();
      return view('contabilidad.calendareventos.ver_calendario');
    
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

      //Valores recibidos via ajax
      $title = $_POST['title'];
      $description = $_POST['description'];
      $start = $_POST['start'];
      $end = $_POST['end'];
      $allDay = $_POST['allDay'];
      $className = $_POST['className'];
      $icon = $_POST['icon'];
      
      //Insertando evento a base de datos
      $evento = new Calendarevento;
      
      $evento->title = $title;        
      $evento->description = $description;     
      $evento->start = Carbon::now();
      $evento->end = Carbon::now();
      $evento->allDay = 1;       
      $evento->className = $className;
      $evento->icon = $icon;
      
      $evento->save();
    
    }  

  /*************************************************************************************
   * Actualiza registro
   ************************************************************************************/
  public function update($id)
  {

    DB::beginTransaction();
    try {    
      //dd(Input::get());
      $input = Input::all();
      $rules = array(
        'user_id' => 'required'
        //'start' => 'required|date_format:d/m/Y G:ia',
        //'end' => 'required|date_format:d/m/Y G:ia'
      );

      $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
      ];        
          
      $validation = \Validator::make($input, $rules, $messages);        

      if ($validation->passes())
      {
        $dato = Calendarevento::find($id);
        $dato->user_id = Input::get('user_id');
        //$dato->start = Carbon::createFromFormat('d/m/Y G:ia', Input::get('start'));
        //$dato->end = Carbon::createFromFormat('d/m/Y G:ia', Input::get('end'));  
        $dato->allDay = Input::has('allDay');
        Sity::RegistrarEnBitacora($dato, Input::get(), 'Calendarevento', 'Actualiza evento');
        $dato->save();      

        DB::commit();

        Session::flash('success', 'El evento ha sido editado con éxito.');
        return redirect()->route('calendareventos.index');
      }
      return back()->withInput()->withErrors($validation);

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en CalendareventosController.update, la transaccion ha sido cancelada!');
      return back()->withInput();
    }
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

      //dd(Input::all());
      $input = Input::all();
      
      if (Input::get('trantipo_id') == 1) {
        $rules = array(
          'fecha' => 'required', 
          'start' => 'required',  
          'end' => 'required',          
          'un_id' => 'required|Numeric|min:1',
          'am_id' => 'required|Numeric|min:1',
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'chqno' => 'Required'
        );      

      } elseif (Input::get('trantipo_id') == 5) {
        $rules = array(
          'fecha' => 'required', 
          'start' => 'required',  
          'end' => 'required',          
          'un_id' => 'required|Numeric|min:1',
          'am_id' => 'required|Numeric|min:1',
          'trantipo_id' => 'Required'
        );  
      
      } else {
        $rules = array(
          'fecha' => 'required', 
          'start' => 'required',  
          'end' => 'required',          
          'un_id' => 'required|Numeric|min:1',
          'am_id' => 'required|Numeric|min:1',
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'transno' => 'Required'
        );  
      }

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
        //dd(Input::get('fecha'), Carbon::createFromFormat('d/m/Y', Input::get('fecha'))->toFormattedDateString()     );
        
        // verifica que exista un periodo de acuerdo a la fecha de pago
        $year = Carbon::createFromFormat('d/m/Y', Input::get('fecha'))->year;
        $month = Carbon::createFromFormat('d/m/Y', Input::get('fecha'))->month;
        $pdo = Sity::getMonthName($month).'-'.$year; 

        // encuentra el periodo mas antiguo abierto
        $periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
        //dd( $pdo, $periodo);
          
        // se puede registrar reservaciones de amenidades en cualquier fecha del presento o futuro, pero el pago del deposito 
        // debe corresponder al presente periodo contable
        if ($pdo != $periodo->periodo) {
          Session::flash('danger', '<< ERROR >> Solamente se permite hacer pagos de deposito por reservacion que correspondan al periodo vigente de '.$periodo->periodo);
          return back()->withInput()->withErrors($validation);
        }
        
        // calcula el monto de deposito o alquiler de la amenidad
        $monto = Am_alquilere::find(Input::get('am_id'));
        
        // si el tipo de transaccion es efectivo no es necesario banco ni transaccion numero
        // Registra el pago recibido          
        if (Input::get('trantipo_id') == 1) {

          $dato = new Pago;
          $dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          $dato->trans_no    = Input::get('chqno'); 
          $dato->monto       = $monto->deposito; 
          $dato->f_pago      = Carbon::createFromFormat('d/m/Y', Input::get('fecha'));
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = Input::get('un_id');
          $dato->user_id     = Auth::user()->id;        
          $dato->save();
      
        } elseif (Input::get('trantipo_id') == 5) {

          $dato = new Pago;
          //$dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          //$dato->trans_no    = Input::get('transno'); 
          $dato->monto       = $monto->deposito; 
          $dato->f_pago      = Carbon::createFromFormat('d/m/Y', Input::get('fecha'));
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = Input::get('un_id');
          $dato->user_id     = Auth::user()->id;        
          $dato->save();
        
        } else {

          $dato = new Pago;
          $dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          $dato->trans_no    = Input::get('transno'); 
          $dato->monto       = $monto->deposito; 
          $dato->f_pago      = Carbon::createFromFormat('d/m/Y', Input::get('fecha'));
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = Input::get('un_id');
          $dato->user_id     = Auth::user()->id;        
          $dato->save();            

        }

        // encuentra por lo menos uno de los propietarios
        $props = Prop::where('un_id', Input::get('un_id'))
         ->join('users','users.id','=','props.user_id')
         ->first();
        //dd($props);

        $evento = new Calendarevento;

        $evento->title = Am_alquilere::find(Input::get('am_id'))->nombre;
        $evento->start = Carbon::createFromFormat('d/m/Y G:ia', Input::get('start'));
        $evento->end = Carbon::createFromFormat('d/m/Y G:ia', Input::get('end'));
        $evento->un_id = Input::get('un_id');
        $evento->user_id = $props->id;
        $evento->am_id = Input::get('am_id');
        $evento->description = Input::get('descripcion');
        $evento->res_pago_id =  $dato->id;
        $evento->res_fechapago =  Carbon::createFromFormat('d/m/Y', Input::get('fecha'));
        $evento->res_tipopago = Input::get('trantipo_id');
        $evento->res_monto = $monto->deposito;
        $evento->pc_monto = $monto->alquiler;        
        $evento->className = 'bg-color-yellow txt-color-white';
        $evento->icon = 'fa-unlock-o';
        if (Input::get('trantipo_id') == 1) {
          $evento->res_docno = Input::get('chqno');
        } elseif (Input::get('trantipo_id') == 5) {
          $evento->res_docno = 'n/a';  
        } else {
          $evento->res_docno = Input::get('transno');    
        } 
        
        $evento->save();
        
        // contabiliza evento como reservado
        Npago::contabilizaReservaAm($evento, $dato->id, $periodo->id);

        // Registra en Detallepago para generar un renglon en el recibo
        //Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);
        
        Sity::RegistrarEnBitacora($evento, Input::get(), 'Calendarevento', 'Registra nueva resercacion de amenidades');
      
        Session::flash('success', 'La reservacion No. ' .$evento->id. ' ha sido registrado con éxito.');
        DB::commit();       

        return redirect()->route('calendareventos.index');
      }       
  
      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);

    } catch (\Exception $e) {
        DB::rollback();
        Session::flash('warning', ' Ocurrio un error en el modulo CalendereventosController.store, la transaccion ha sido cancelada! '.$e->getMessage());
        return back()->withInput();
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
  public function edit($calendarevento_id)
  {
    // encuentra los datos generales del evento
    $dato = Calendarevento::find($calendarevento_id);      

    $dato->start = Date::parse($dato->start)->toDayDateTimeString();
    $dato->end = Date::parse($dato->end)->toDayDateTimeString();

    // cambia el campo am_id por el nombre de la amenidad
    $dato->am_id = Am_alquilere::find($dato->am_id)->nombre;
    $dato->un_id = Un::find($dato->un_id)->codigo;
    //dd($dato);

    // encuentra el o los propietarios de la unidad
    $props = Prop::where('un_id', $dato->un_id)
             ->join('users','users.id','=','props.user_id')
             ->select('users.id', 'cedula', 'nombre_completo')
             ->get();
    //dd($props);
    
    // concatena toda la informacion del propietario dentro del item cedula
    $props->map(function ($props) {
      $props->cedula = $props->cedula.' '.$props->nombre_completo;
    });      
    //dd($props);
    
    // prepara los datos de propietarios para ser utilizados en un combo box
    $props = $props->pluck('cedula', 'id')->all();
    // dd($props);
    
    return view('contabilidad.calendareventos.edit')
        ->with('props', $props) 
        ->with('dato', $dato); 
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function actualizaEvento()
  {

    //dd(Input::all());
/*      //Valores recibidos via ajax
    $id = $_POST['id'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $allDay = $_POST['allDay'];
    dd($id, $start, $end, $allDay);
    Log::info([$id, $start, $end, $allDay]);
    
    $evento = Calendarevento::find($id);
    
    $evento->start = $start;        
    if($end){
        $evento->end = $end;
    }
    $evento->allDay = $allDay;
    
    $evento->save();*/
  
    DB::beginTransaction();
    try {
      //dd(Input::get());
      $input = Input::all();
      $rules = array(
        'start'      => 'required|date',
        'end'      => 'required|date'       
      );

      $messages = [
        'required' => 'El campo :attribute es requerido!',
        'unique'   => 'Este :attribute ya existe, no se admiten duplicados!'
      ];        
          
      $validation = \Validator::make($input, $rules, $messages);        

      if ($validation->passes()) {
        
        $dato = Calendarevento::find(Input::get('id'));
        $dato->start = Input::get('start');
        $dato->end = Input::get('end');;
        
        Sity::RegistrarEnBitacora($dato, Input::get(), 'Calendarevento', 'Actualiza evento');        
        
        $dato->save();      

        DB::commit();
        
        Session::flash('success',' El evento ha sido actualizado con exito');
        return redirect()->route('calendareventos.index');
      }

      Session::flash('danger', ' Se encontraron errores en su formulario, intente nuevamente.');
      return back()->withInput()->withErrors($validation);
    
    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', 'Ocurrio un error en el modulo CalendareventosController.update, la transaccion ha sido cancelada! '.$e->getMessage());
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
      //Valor id recibidos via ajax
      $id = $_POST['id'];

      Calendarevento::destroy($id);
  }

  
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function verFullCalendar()
  {

    return view('contabilidad.calendareventos.index');

  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function eventoDevolucion($calendarevento_id, $cancelar)
  {
    //dd($calendarevento_id);

    $dato = Calendarevento::find($calendarevento_id);
    $dato->am_id = Am_alquilere::find($dato->am_id)->codigo;
    $dato->un_id = Un::find($dato->un_id)->codigo; 
    //dd($dato);
    
    if ($cancelar == true && $dato->res_pago_id && $dato->pc_pago_id == null) {    // se trata de una devolucion del deposito por cancelacion de reservacion
      $mensaje = 'Se devolvera deposito de B/.'.$dato->res_monto.' por cancelacion de reservacion de '.$dato->title.'.'; 
    
    } elseif ($cancelar == true && $dato->res_pago_id && $dato->pc_pago_id) {    // se trata de una devolucion de deposito y alquiler por cancelacion de evento
      $mensaje = 'Se devolvera la suma total de B/.'.number_format(($dato->res_monto + $dato->pc_monto),2).' por cancelacion de reservacion de '.$dato->title.'. Desglosados de la siguiente forma: B/.'.$dato->res_monto.' de deposito y B/.'.$dato->pc_monto.' de alquiler.';
    
    } elseif ($cancelar == false && $dato->status == 3) {    // se trata de una devolucion de deposito por culminacion exitosa de evento    
      $mensaje = 'Se devolvera deposito de B/.'.$dato->res_monto.' por culminacion exitosa de reservacion de '.$dato->title.'.'; 
    }
    
    // obtiene todas las instituciones bancarias actualmente registrada
    $bancos = Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
    //dd($bancos);
    
    // obtiene todos los diferentes tipos de pagos
    $trantipos= Trantipo::pluck('nombre', 'id')->all();
    $trantipos= Trantipo::orderBy('nombre')->get();   
    //dd($trantipos); 
    
    return view('contabilidad.calendareventos.eventoDevolucion')
              ->with('trantipos', $trantipos)
              ->with('bancos', $bancos)
              ->with('mensaje', $mensaje)
              ->with('cancelar', $cancelar)
              ->with('dato', $dato);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function eventoDevolucionStore($calendarevento_id)
  {

    DB::beginTransaction();
    try {

      //dd(Input::all());
      $input = Input::all();
      
      if (Input::get('trantipo_id') == 1) {
        $rules = array(
          'fecha' => 'required|date', 
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'chqno' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );      

      } elseif (Input::get('trantipo_id') == 5) {
        $rules = array(
          'fecha' => 'required|date', 
          'trantipo_id' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );  
      
      } else {
        $rules = array(
          'fecha' => 'required|date', 
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'transno' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );  
      }

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
        $year = Carbon::parse(Input::get('fecha'))->year;
        $month = Carbon::parse(Input::get('fecha'))->month;
        $pdo = Sity::getMonthName($month).'-'.$year; 

        // encuentra el periodo mas antiguo abierto
        $periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
        //dd($periodo);
          
        // se puede registrar reservaciones de amenidades en cualquier fecha del presento o futuro, pero el pago del deposito 
        // debe corresponder al presente periodo contable
        if ($pdo != $periodo->periodo) {
          Session::flash('danger', '<< ERROR >> Solamente se permite hacer devoluciones de depositos por reservacion que correspondan al periodo vigente de '.$periodo->periodo);
          return back()->withInput()->withErrors($validation);
        }
        
        // encuentra los datos del evento
        $evento = Calendarevento::find($calendarevento_id);

        if (Input::get('cancelar') == 1 && $evento->res_pago_id && $evento->pc_pago_id == null) {    // se trata de una devolucion del deposito por cancelacion de reservacion

          // actualiza el status a cancelado
          $evento->status = 5;
          $evento->save();

          $devolucion = new Eventodevolucione;
          
          if (Input::get('trantipo_id') == 1) {
            $doc_no = Input::get('chqno');
          } elseif (Input::get('trantipo_id') == 5) {
            $doc_no = 'n/a';  
          } else {
            $doc_no = Input::get('transno');    
          }           
          $devolucion->fecha = Input::get('fecha');
          $devolucion->detalle = 'Devuelve deposito por cancelacion '.$evento->title.', '.Trantipo::find(Input::get('trantipo_id'))->siglas.', '.$docno;
          $devolucion->catalogo_id = 39;
          $devolucion->trantipo_id = Input::get('trantipo_id');
          $devolucion->banco_id = Input::get('banco_id');
          $devolucion->monto = $evento->res_monto;
          $devolucion->calendarevento_id = Input::get('calendarevento_id');
          $devolucion->doc_no = $doc_no;
          
          $devolucion->save();
        
          // contabiliza devolucion de deposito
          Npago::contabilizaDevolucionDeposito($devolucion, $periodo);
        
        } elseif (Input::get('cancelar') == 1 && $evento->res_pago_id && $evento->pc_pago_id) {    // se trata de una devolucion de deposito y alquiler por cancelacion de evento
          
          // actualiza el status a cancelado
          $evento->status = 5;
          $evento->save();

          $devolucion = new Eventodevolucione;
          
          if (Input::get('trantipo_id') == 1) {
            $doc_no = Input::get('chqno');
          } elseif (Input::get('trantipo_id') == 5) {
            $doc_no = 'n/a';  
          } else {
            $doc_no = Input::get('transno');    
          } 
          $devolucion = new Eventodevolucione;
          $devolucion->fecha = Input::get('fecha');
          $devolucion->detalle = 'Devuelve deposito por cancelacion '.$evento->title.', '.Trantipo::find(Input::get('trantipo_id'))->siglas.', '.$docno;
          $devolucion->catalogo_id = 39;
          $devolucion->trantipo_id = Input::get('trantipo_id');
          $devolucion->banco_id = Input::get('banco_id');
          $devolucion->monto = $evento->res_monto;
          $devolucion->calendarevento_id = Input::get('calendarevento_id');
          $devolucion->doc_no = $doc_no;

          $devolucion->save();
        
          // contabiliza devolucion de deposito
          Npago::contabilizaDevolucionDeposito($devolucion, $periodo);
       
          $devolucion = new Eventodevolucione;
          
          if (Input::get('trantipo_id') == 1) {
            $doc_no = Input::get('chqno');
          } elseif (Input::get('trantipo_id') == 5) {
            $doc_no = 'n/a';  
          } else {
            $doc_no = Input::get('transno');    
          } 
          $devolucion->fecha = Input::get('fecha');
          $devolucion->detalle = 'Devuelve alquiler por cancelacion '.$evento->title.', '.Trantipo::find(Input::get('trantipo_id'))->siglas.', '.$docno;
          $devolucion->catalogo_id = 45;
          $devolucion->trantipo_id = Input::get('trantipo_id');
          $devolucion->banco_id = Input::get('banco_id');
          $devolucion->monto = $evento->pc_monto;
          $devolucion->calendarevento_id = Input::get('calendarevento_id');
          $devolucion->doc_no = $doc_no;

          $devolucion->save();
        
          // contabiliza devolucion de deposito
          Npago::contabilizaDevolucionAlquiler($devolucion, $periodo); 
        
        } elseif (Input::get('cancelar') == 0 && $evento->status == 3) {    // se trata de una devolucion de deposito por culminacion exitosa de evento

          // actualiza el status a cancelado
          $evento->className = 'bg-color-darken txt-color-white';
          $evento->icon = 'fa-lock';
          $evento->status = 4;
          $evento->save();

          $devolucion = new Eventodevolucione;
          
          if (Input::get('trantipo_id') == 1) {
            $doc_no = Input::get('chqno');
          } elseif (Input::get('trantipo_id') == 5) {
            $doc_no = 'n/a';  
          } else {
            $doc_no = Input::get('transno');    
          } 

          $devolucion->fecha = Input::get('fecha');
          $devolucion->detalle = 'Devuelve deposito por culminacion de reserva de '.$evento->title.', '.Trantipo::find(Input::get('trantipo_id'))->siglas.', '.$docno;
          $devolucion->catalogo_id = 39;
          $devolucion->trantipo_id = Input::get('trantipo_id');
          $devolucion->banco_id = Input::get('banco_id');
          $devolucion->monto = $evento->res_monto;
          $devolucion->calendarevento_id = Input::get('calendarevento_id');
          $devolucion->doc_no = $doc_no;

          $devolucion->save();
        
          // contabiliza devolucion de deposito
          Npago::contabilizaDevolucionDeposito($devolucion, $periodo);

        }

        $periodo = 1;
        
        //Sity::RegistrarEnBitacora($devolucioneevento, Input::get(), 'Calendarevento', 'Registra nueva resercacion de amenidades');
      
        Session::flash('success', 'La devolucion ha sido registrado con éxito.');
        DB::commit();       

        return redirect()->route('calendareventos.index');
      }       
  
      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CalendereventosController.eventoDevolucionStore, la transaccion ha sido cancelada! '.$e->getMessage());
      return back()->withInput();
    }

  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function eventoAlquiler($calendarevento_id)
  {
    //dd($calendarevento_id);

    $dato = Calendarevento::find($calendarevento_id);
    $dato->am_id = Am_alquilere::find($dato->am_id)->codigo;
    $dato->un_id = Un::find($dato->un_id)->codigo; 
    //dd($dato);
    
    $mensaje = 'Se registrara la suma de B/.'.$dato->pc_monto.' para cancelar el pago de alquiler de '.$dato->title.'.'; 
    
    // obtiene todas las instituciones bancarias actualmente registrada
    $bancos = Banco::orderBy('nombre')->pluck('nombre', 'id')->all();
    //dd($bancos);
    
    // obtiene todos los diferentes tipos de pagos
    $trantipos= Trantipo::pluck('nombre', 'id')->all();
    $trantipos= Trantipo::orderBy('nombre')->get();   
    //dd($trantipos); 
    
    return view('contabilidad.calendareventos.eventoAlquiler')
              ->with('trantipos', $trantipos)
              ->with('bancos', $bancos)
              ->with('mensaje', $mensaje)
              ->with('dato', $dato);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function eventoAlquilerUpdate($calendarevento_id)
  {

    DB::beginTransaction();
    try {

      //dd(Input::all());
      $input = Input::all();
      
      if (Input::get('trantipo_id') == 1) {
        $rules = array(
          'fecha' => 'required|date', 
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'chqno' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );      

      } elseif (Input::get('trantipo_id') == 5) {
        $rules = array(
          'fecha' => 'required|date', 
          'trantipo_id' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );  
      
      } else {
        $rules = array(
          'fecha' => 'required|date', 
          'banco_id' => 'Required',
          'trantipo_id' => 'Required',
          'transno' => 'Required'
          //'monto' => 'required|Numeric|min:1'
        );  
      }

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
        $year = Carbon::parse(Input::get('fecha'))->year;
        $month = Carbon::parse(Input::get('fecha'))->month;
        $pdo = Sity::getMonthName($month).'-'.$year; 

        // encuentra el periodo mas antiguo abierto
        $periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
        //dd($periodo);
          
        // se puede registrar reservaciones de amenidades en cualquier fecha del presento o futuro, pero el pago del deposito 
        // debe corresponder al presente periodo contable
        if ($pdo != $periodo->periodo) {
          Session::flash('danger', '<< ERROR >> Solamente se permite hacer pagos de alquileres de amenidades que correspondan al periodo vigente de '.$periodo->periodo);
          return back()->withInput()->withErrors($validation);
        }

        // actualiza el status a alquilado
        $evento = Calendarevento::find($calendarevento_id);
      
        // calcula el monto de deposito o alquiler de la amenidad
        $monto = Am_alquilere::find($evento->am_id);

        // si el tipo de transaccion es efectivo no es necesario banco ni transaccion numero
        // Registra el pago recibido          
        if (Input::get('trantipo_id') == 1) {

          $dato = new Pago;
          $dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          $dato->trans_no    = Input::get('chqno'); 
          $dato->monto       = $monto->alquiler; 
          $dato->f_pago      = Input::get('fecha');
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = $evento->un_id;
          $dato->user_id     = Auth::user()->id;        
          $dato->save();
      
        } elseif (Input::get('trantipo_id') == 5) {

          $dato = new Pago;
          //$dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          //$dato->trans_no    = Input::get('transno'); 
          $dato->monto       = $monto->alquiler; 
          $dato->f_pago      = Input::get('fecha');
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = $evento->un_id;
          $dato->user_id     = Auth::user()->id;        
          $dato->save();
        
        } else {

          $dato = new Pago;
          $dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          $dato->trans_no    = Input::get('transno'); 
          $dato->monto       = $monto->alquiler; 
          $dato->f_pago      = Input::get('fecha');
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->concepto    = 'deposito para alquiler de amenidades';   
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = $evento->un_id;
          $dato->user_id     = Auth::user()->id;        
          $dato->save();            

        }

        // actualiza los datos del evento
        $evento->pc_pago_id =  $dato->id;
        $evento->pc_fechapago = Input::get('fecha');
        $evento->pc_tipopago = Input::get('trantipo_id');
        if (Input::get('trantipo_id') == 1) {
          $evento->pc_docno = Input::get('chqno');
        } elseif (Input::get('trantipo_id') == 5) {
          $evento->pc_docno = 'n/a';  
        } else {
          $evento->pc_docno = Input::get('transno');    
        } 
        $evento->className = 'bg-color-purple txt-color-white';
        $evento->icon = 'fa-unlock-o';
        $evento->status = 2;
        
        $evento->save();

        $periodo = 1;
        
        // contabiliza evento como alquilado
        Npago::contabilizaAlquilerAm($evento, $dato->id, $periodo);

        //Sity::RegistrarEnBitacora($devolucioneevento, Input::get(), 'Calendarevento', 'Registra nueva resercacion de amenidades');
      
        Session::flash('success', 'Pago por alquiler de amenidad ha sido registrado con éxito.');
        DB::commit();       

        return redirect()->route('calendareventos.index');
      }       
  
      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);

    } catch (\Exception $e) {
      DB::rollback();
      Session::flash('warning', ' Ocurrio un error en el modulo CalendereventosController.eventoAlquilerUpdate, la transaccion ha sido cancelada! '.$e->getMessage());
      return back()->withInput();
    }

  }

}