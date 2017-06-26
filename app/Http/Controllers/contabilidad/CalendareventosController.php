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
 
      $datos = Calendarevento::all();

      $datos->map(function ($datos) {
        $datos->start = Date::parse($datos->start)->toDayDateTimeString();
        $datos->end = Date::parse($datos->end)->toDayDateTimeString();
        $datos->am_id = Am_alquilere::find($datos->am_id)->codigo;
        $datos->un_id = Un::find($datos->un_id)->codigo;       
        $props = User::find($datos->user_id);
        
        // agrega el nuevo elemento a la collection
        $datos->props = $props->cedula.' '.$props->nombre_completo;
      });
      // dd($datos);

      //Encuentra todas las amenidades
      $ams = Am_alquilere::orderBy('nombre')->pluck('nombre', 'id')->All();
      //dd($ams);

      // encuentra todas las unidades disponibles
      $uns = Un::orderBy('codigo')->get();
      
      $uns->map(function ($uns) {
        
        // encuentra el o los propietarios de la unidad
        $props = Prop::where('un_id', $uns->id)
                 ->join('users','users.id','=','props.user_id')
                 ->select('cedula', 'cedula', 'nombre_completo')
                 ->get();
        
        $propietarios = "";
        foreach ($props as $prop) {
          if ($propietarios == "") {
            $propietarios = $uns->codigo.' '.$prop->cedula.' '.$prop->nombre_completo;
          }
          else {
            $propietarios = $uns->codigo.' '.$propietarios.', '.$prop->cedula. ' '.$prop->nombre_completo;         
          }
        }
      
        $uns->props = $propietarios;
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
                ->with('trantipos', $trantipos)
                ->with('datos', $datos); 
    }

    public function cargaEventos()
    {
      
      $data = Calendarevento::get(['id','title','un_id','description','start','end','allDay','className','icon']);
 
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
      //DB::beginTransaction();
      //try {

        //dd(Input::all());
        $input = Input::all();
        
        if (Input::get('trantipo_id') == 1) {
          $rules = array(
            'fecha' => 'required|date', 
            'start' => 'required',  
            'end' => 'required',          
            'un_id' => 'required|Numeric|min:1',
            'am_id' => 'required|Numeric|min:1',
            'trantipo_id' => 'Required',
            'chqno' => 'Required'
          );      

        } elseif (Input::get('trantipo_id') == 5) {
          $rules = array(
            'fecha' => 'required|date', 
            'start' => 'required',  
            'end' => 'required',          
            'un_id' => 'required|Numeric|min:1',
            'am_id' => 'required|Numeric|min:1',
            'trantipo_id' => 'Required'
          );  
        
        } else {
          $rules = array(
            'fecha' => 'required|date', 
            'start' => 'required',  
            'end' => 'required',          
            'un_id' => 'required|Numeric|min:1',
            'am_id' => 'required|Numeric|min:1',
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

          // encuentra el periodo mas antiguo abierto
          //$periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
          //dd($periodo);
            
          // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
          //if ($pdo != $periodo->periodo) {
            //Session::flash('danger', '<< ERROR >> Solamente se permite registrar facturas de gastos que correspondan al periodo vigente de '.$periodo->periodo);
            //return back()->withInput()->withErrors($validation);
          //}
          
          //dd(Input::get('start'), Carbon::createFromFormat('d/m/Y h:i A', Input::get('start')));

          
          /*// calcula el periodo al que corresponde la fecha de pago
          $f_pago= Carbon::parse(Input::get('f_pago'));
          $year= $f_pago->year;
          $month= $f_pago->month;
          $pdo= Sity::getMonthName($month).'-'.$year;    
            
          // encuentra el periodo mas antiguo abierto
          $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
          //dd($pdo, $periodo->periodo);
          
          // solamente se permite registrar pagos que correspondan al periodo mas antiguo abierto
          if ($pdo != $periodo->periodo) {
            Session::flash('danger', '<< ERROR >> Solamente se permite registrar pagos que correspondan al periodo de '.$periodo->periodo);
            return back()->withInput()->withErrors($validation);
          }

          // antes de iniciar el proceso de pago, ejecuta el proceso de penalizacion
          Npago::penalizarTipo2(Input::get('f_pago'), Input::get('un_id'), $periodo->id);

          // Almacena el monto de la transaccion
          $montoRecibido= round(floatval(Input::get('monto')),2);

          // Procesa el pago recibido si el tipo de transaccion es cheque
          if (Input::get('key') == 1) {
            // Solamente registra el pago recibido no lo procesa
            $dato = new Pago;
            $dato->banco_id    = Input::get('banco_id');
            $dato->trantipo_id = Input::get('key');
            $dato->trans_no    = Input::get('transno'); 
            $dato->monto       = $montoRecibido;
            $dato->f_pago      = Input::get('f_pago');
            $dato->descripcion = Input::get('descripcion');
            $dato->fecha       = Carbon::today();         
            $dato->entransito  = 1;
            $dato->un_id       = Input::get('un_id');
            $dato->user_id     = Auth::user()->id;        
            $dato->save();
            
            // Registra en bitacoras
            Sity::RegistrarEnBitacora($dato, Input::get(), 'Pago', 'Registra pago de propietario');

            DB::commit(); 
            Session::flash('success', 'El pago ' .$dato->id. ' ha sido creado con éxito.');     
        
          } else {
            
            // Registra el pago recibido
            $dato = new Pago;
            $dato->banco_id    = Input::get('banco_id');
            $dato->trantipo_id = Input::get('key');
            $dato->trans_no    = Input::get('transno'); 
            $dato->monto       = $montoRecibido;
            $dato->f_pago      = Input::get('f_pago');
            $dato->descripcion = Input::get('descripcion');
            $dato->fecha       = Carbon::today();         
            $dato->entransito  = 0;
            $dato->un_id       = Input::get('un_id');
            $dato->user_id     = Auth::user()->id;        
            $dato->save();
          }*/

          // Registra el pago recibido
          $dato = new Pago;
          $dato->banco_id    = Input::get('banco_id');
          $dato->trantipo_id = Input::get('trantipo_id');
          $dato->trans_no    = Input::get('transno'); 
          $dato->monto       = Input::get('monto'); 
          $dato->f_pago      = Input::get('fecha');
          $dato->descripcion = 'Deposito por alquiler de amenidades';
          $dato->fecha       = Carbon::today();         
          $dato->entransito  = 0;
          $dato->un_id       = Input::get('un_id');
          $dato->user_id     = Auth::user()->id;        
          $dato->save();

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
          $evento->res_tipopago = Input::get('trantipo_id');
          $evento->res_monto = Input::get('monto');
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
          
          $periodo = 1;
          
          // contabiliza evento como reservado
          Npago::contabilizaReservaAm($evento, $periodo, $dato->id);

          // Registra en Detallepago para generar un renglon en el recibo
          //Self::registraDetallepago($periodo, $ocobro, 'Paga cuota de mantenimiento regular '. $mesAnio.' (vence: '.Date::parse($dato->f_vencimiento)->toFormattedDateString().')', $dato->id, $importe, $un_id, $pago_id, self::getLastNoDetallepago($pago_id), 1);
          

          Sity::RegistrarEnBitacora($evento, Input::get(), 'Calendarevento', 'Registra nueva resercacion de amenidades');
        
          Session::flash('success', 'La reservacion No. ' .$evento->id. ' ha sido registrado con éxito.');
          DB::commit();       

          return redirect()->route('calendareventos.index');
        }       
    
        Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
        return back()->withInput()->withErrors($validation);

      //} catch (\Exception $e) {
          //DB::rollback();
          //Session::flash('warning', ' Ocurrio un error en el modulo CalendereventosController.store, la transaccion ha sido cancelada! '.$e->getMessage());
          //return back()->withInput();
      //}
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
    
      //DB::beginTransaction();
      //try {
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
      
      //} catch (\Exception $e) {
        //DB::rollback();
        //Session::flash('warning', 'Ocurrio un error en el modulo CalendareventosController.update, la transaccion ha sido cancelada! '.$e->getMessage());
        //return back()->withInput();
      //}      
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

}
