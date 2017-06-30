<?php

namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Jenssegers\Date\Date;
use Session, DB, Cache;
use App\library\Sity;

use App\Diariocaja;
use App\Pago;
use App\Ctmayore;
use App\Detallepagofactura;
use App\Ctdiario;
use App\Pcontable;
use App\Catalogo;

class DiariocajasController extends Controller
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
  
      $datos = Diariocaja::all();
      return view('contabilidad.diariocajas.index')->withDatos($datos);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $fecha= Diariocaja::find($id)->fecha;
      //dd($fecha);

      // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque,Efectivo y tarjetas de credito)
      $ingresoEfectivos = Pago::where('f_pago', $fecha)
                ->where(function($query){
                          return $query
                            ->where('trantipo_id', 1)
                            ->orWhere('trantipo_id', 5)
                            ->orWhere('trantipo_id', 6)
                            ->orWhere('trantipo_id', 7);
                  })
                ->join('ctmayores', function($join)
                  {
                    $join->on('pagos.id', '=', 'ctmayores.pago_id')
                         ->where('tipo', 1)
                         ->where('debito', '>', 0);
                  })
                ->join('trantipos', function($join)
                  {
                    $join->on('pagos.trantipo_id', '=', 'trantipos.id');
                  })
                ->get(['pagos.id as pagoNo', 'trantipos.id as trantipo_id', 'trantipos.nombre','codigo','trans_no','detalle','debito as monto']);
      //dd($ingresoEfectivos->toArray()); 
      
      // calcula el total de ingresos recibidos por efectivo y cheques
      $totalIngresoEfectivos = $ingresoEfectivos->sum('monto');  
      //dd($totalIngresoEfectivos); 
      
      // calcula el total de ingresos recibidos por efectivo solamente
      $totalEfectivos = $ingresoEfectivos->where('trantipo_id', 5)->sum('monto');  
      //dd($totalEfectivos);
      
      // calcula el total de ingresos recibidos por cheque solamente
      $totalCheques = $ingresoEfectivos->where('trantipo_id', 1)->sum('monto');  
      //dd($totalCheques);

      // calcula el total de ingresos recibidos por tarjetas clave
      $totalClaves = $ingresoEfectivos->Where('trantipo_id', 6)->sum('monto');  
      //dd($totalClaves);  

      // calcula el total de ingresos recibidos por tarjetas de credito solamente
      $totalTarjetas = $ingresoEfectivos->Where('trantipo_id', 7)->sum('monto');       
      //dd($totalTarjetas);      

      // encuentra los datos para la seccion de Desembolsos de efectivo del Informe de caja diario (Cheque,Efectivo y tarjetas de credito)
      // 1. encuentra la cantidad de efectivo desembolsado
      $desembolsoEfectivos = Detallepagofactura::where('detallepagofacturas.fecha', $fecha)
                ->where(function($query){
                          return $query
                          ->where('trantipo_id', 1)
                          ->orWhere('trantipo_id', 5)
                          ->orWhere('trantipo_id', 6)
                          ->orWhere('trantipo_id', 7);
                                        })                
                ->join('ctmayores', function($join)
                  {
                    $join->on('detallepagofacturas.id', '=', 'ctmayores.detallepagofactura_id')
                         ->where('tipo', 1)
                         ->where('credito', '>', 0);
                  })
                ->join('trantipos', function($join)
                  {
                    $join->on('detallepagofacturas.trantipo_id', '=', 'trantipos.id');
                  })
                ->get(['detallepagofacturas.id as pagoNo','trantipos.id as trantipo_id', 'trantipos.nombre as trantipo','doc_no','codigo','ctmayores.detalle','credito as monto']);
      
      dd($desembolsoEfectivos->toArray()); 

      // calcula el total desembolsado en efectivo solamente      
      $totalDesembolsoEfectivos = $desembolsoEfectivos->sum('monto');  
      //dd($totalDesembolsoEfectivos);
      
      // calcula el total de desembolsado en efectivo solamente
      $totalDesemEfectivos = $desembolsoEfectivos->where('trantipo_id', 5)->sum('monto');  
      //dd($totalDesemEfectivos);
      
      // calcula el total de desembolsado en cheque solamente
      $totalDesemCheques = $desembolsoEfectivos->where('trantipo_id', 1)->sum('monto');  
      //dd($totalDesemCheques);

      // calcula el total de desembolsado en tarjetas clave
      $totalDesemClaves = $desembolsoEfectivos->Where('trantipo_id', 6)->sum('monto');  
      //dd($totalDesemClaves);  

      // calcula el total de desembolsado en tarjetas de credito solamente
      $totalDesemTarjetas = $desembolsoEfectivos->Where('trantipo_id', 7)->sum('monto');       
      //dd($totalDesemTarjetas);   
      
      $fecha= Date::parse($fecha)->toFormattedDateString();

      return view('contabilidad.diariocajas.show')
                  ->with('ingresoEfectivos', $ingresoEfectivos)
                  ->with('totalEfectivos', $totalEfectivos)
                  ->with('totalCheques', $totalCheques)
                  ->with('totalClaves', $totalClaves)
                  ->with('totalTarjetas', $totalTarjetas)                                                  
                  ->with('totalIngresoEfectivos', $totalIngresoEfectivos)                                                  
                  
                  ->with('desembolsoEfectivos', $desembolsoEfectivos)                                                  
                  ->with('totalDesemEfectivos', $totalDesemEfectivos)
                  ->with('totalDesemCheques', $totalDesemCheques)
                  ->with('totalDesemClaves', $totalDesemClaves)
                  ->with('totalDesemTarjetas', $totalDesemTarjetas)
                  ->with('totalDesembolsoEfectivos', $totalDesembolsoEfectivos)
                  ->with('fecha', $fecha);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $diariocaja = Diariocaja::find($id);
      return view('contabilidad.diariocajas.edit')->with('diariocaja', $diariocaja);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $diariocaja_id
     * @return \Illuminate\Http\Response
     */
    public function update($diariocaja_id)
    {

      DB::beginTransaction();
      try {
        
        //dd(Input::all());
        $input = Input::all();

        if (Input::get('arqueocc_radios') == '1') {
          $rules = array(
          );
        
        } elseif (Input::get('arqueocc_radios') == '2') {
          $rules = array(
            'montofaltante' => 'required|Numeric|min:0.01'          
          );
        
        } elseif (Input::get('arqueocc_radios') == '3') {
          $rules = array(
            'montosobrante' => 'required|Numeric|min:0.01'      
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

          if (Input::get('checkbox') == 'on') {
            $aprobado = true;
          } else {
            $aprobado = false;
          }
          
          // aprueba informe
          $diariocaja = Diariocaja::find($diariocaja_id);
          $diariocaja->aprobado = $aprobado;
          $diariocaja->aprobadopor = Cache::get('userFullNamekey');
          $diariocaja->save();
    
          // Registra en bitacoras
          $detalle = 'Aprueba informe de Caja General el dia '.$diariocaja->fecha;
          $tabla = 'diariocajas';
          $registro = $diariocaja->id;
          $accion = 'Aprueba informe de Caja General';
          
          Sity::RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion);

          // contabiliza deposito en Banco        
          // incializa variables a utilizar
          $cuenta_8 = Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
          $cuenta_32 = Catalogo::find(32)->nombre;  // 1000.00 Caja general

          $fecha = Diariocaja::find($diariocaja_id)->fecha;
          //dd($fecha);

          // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque,Efectivo y tarjetas de credito)
          $ingresoEfectivos = Pago::where('f_pago', $fecha)
                    ->where(function($query){
                              return $query
                                ->where('trantipo_id', 1)
                                ->orWhere('trantipo_id', 5)
                                ->orWhere('trantipo_id', 6)
                                ->orWhere('trantipo_id', 7);

                      })
                    ->join('ctmayores', function($join)
                      {
                        $join->on('pagos.id', '=', 'ctmayores.pago_id')
                             ->where('tipo', 1)
                             ->where('debito', '>', 0);
                      })
                    ->join('trantipos', function($join)
                      {
                        $join->on('pagos.trantipo_id', '=', 'trantipos.id');
                      })
                    ->get(['pagos.id as pagoNo', 'trantipos.id as trantipo_id', 'trantipos.nombre','codigo','trans_no','detalle','debito as monto']);
          //dd($ingresoEfectivos->toArray()); 

          // calcula el total de ingresos recibidos por efectivo solamente
          $totalEfectivos = $ingresoEfectivos->where('trantipo_id', 5)->sum('monto');  
          //dd($totalEfectivos);
          
          // calcula el total de ingresos recibidos por cheque solamente
          $totalCheques = $ingresoEfectivos->where('trantipo_id', 1)->sum('monto');  
          //dd($totalCheques);
          
          // calcula el total de ingresos recibidos por cheque solamente
          $totalTajetaDebito = $ingresoEfectivos->where('trantipo_id', 6)->sum('monto');  
          //dd($totalCheques);       
          
          // calcula el total de ingresos recibidos por cheque solamente
          $totalTarjetaCredito = $ingresoEfectivos->where('trantipo_id', 7)->sum('monto');  
          //dd($totalCheques);

          $total = $totalEfectivos + $totalCheques + $totalTajetaDebito + $totalTarjetaCredito;
          
          // encuentra el periodo mas antiguo abierto
          $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();
          //dd($pdo, $periodo->periodo);

          if (Input::get('arqueocc_radios') == '1') {

            // contabiliza el deposito al banco
            // registra en el diario
            // registra un aumento en la cuenta Banco  
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->fecha = $fecha;
            $diario->detalle = $cuenta_8;
            $diario->debito  = $total;
            $diario->credito = Null;
            $diario->save();
            
            // registra en el mayor
            // registra un aumento en la cuenta Banco
            Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, $fecha, 'Para anotar el deposito del efectivo de '.Date::parse($fecha)->toFormattedDateString(), $total);   
                     
            // registra en el diario
            // registra una disminucion en la cuenta de Caja general 
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->detalle = $cuenta_32;
            $diario->debito  = Null;
            $diario->credito = $total;
            $diario->save();

            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = 'Para anotar el deposito del efectivo de '.Date::parse($fecha)->toFormattedDateString();
            $diario->save();
          
          } elseif (Input::get('arqueocc_radios') == '2') {
            // contabiliza el deposito al banco con faltante
            // registra en el diario
            // registra un aumento en la cuenta Banco  
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->fecha = $fecha;
            $diario->detalle = $cuenta_8;
            $diario->debito  = $total - Input::get('montofaltante');
            $diario->credito = Null;
            $diario->save();
            
            // registra en el mayor
            // registra un aumento en la cuenta Banco
            Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, $fecha, 'Para anotar el deposito del efectivo con faltante de '.Date::parse($fecha)->toFormattedDateString(), $total - Input::get('montofaltante'));   
                     
            $dato = new Ctdiario;
            $dato->pcontable_id = $periodo->id;
            $dato->detalle = Catalogo::find(35)->nombre; // Cuentas por cobrar - empleados
            $dato->debito = Input::get('montofaltante');
            $dato->save();
      
            Sity::registraEnCuentas($periodo->id, 'mas', 1, 35, $fecha, 'Para registrar faltante en arqueo de Caja general de '.Date::parse($fecha)->toFormattedDateString(), Input::get('montofaltante'));     

            // registra en el diario
            // registra una disminucion en la cuenta de Caja general
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->detalle = $cuenta_32;
            $diario->debito  = Null;
            $diario->credito = $total;
            $diario->save();

            // registra uns disminucion en la cuenta de Caja general
            Sity::registraEnCuentas($periodo->id, 'menos', 1, 32, $fecha, 'Para anotar el deposito del efectivo de '.Date::parse($fecha)->toFormattedDateString(), $total);     
            
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = 'Para anotar el deposito del efectivo con faltante de '.Date::parse($fecha)->toFormattedDateString();
            $diario->save();          
          
          } elseif (Input::get('arqueocc_radios') == '3') {          
            // contabiliza el deposito al banco con faltante
            // registra en el diario
            // registra un aumento en la cuenta Banco  
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->fecha = $fecha;
            $diario->detalle = $cuenta_8;
            $diario->debito  = $total + Input::get('montosobrante');
            $diario->credito = Null;
            $diario->save();
            
            // registra en el mayor
            // registra un aumento en la cuenta Banco
            Sity::registraEnCuentas($periodo->id, 'mas', 1, 8, $fecha, 'Para anotar el deposito del efectivo con sobrante de '.Date::parse($fecha)->toFormattedDateString(), $total - Input::get('montosobrante'));   
                     
            // registra en Ctdiario principal
            $dato = new Ctdiario;
            $dato->pcontable_id = $periodo->id;
            $dato->detalle = Catalogo::find(33)->nombre; // Otros ingresos
            $dato->credito = Input::get('montosobrante');
            $dato->save();
      
            Sity::registraEnCuentas($periodo->id, 'mas', 1, 33, $fecha, 'Para registrar sobrante en arqueo de Caja general de '.Date::parse($fecha)->toFormattedDateString(), Input::get('montosobrante'));     

            // registra en el diario
            // registra una disminucion en la cuenta de Caja general
            $diario = new Ctdiario;
            $diario->pcontable_id = $periodo->id;
            $diario->detalle = $cuenta_32;
            $diario->debito  = Null;
            $diario->credito = $total;
            $diario->save();

            // registra uns disminucion en la cuenta de Caja general
            Sity::registraEnCuentas($periodo->id, 'menos', 1, 32, $fecha, 'Para anotar el deposito del efectivo de '.Date::parse($fecha)->toFormattedDateString(), $total);     
            
            $diario = new Ctdiario;
            $diario->pcontable_id  = $periodo->id;
            $diario->detalle = 'Para anotar el deposito del efectivo con sobrante de '.Date::parse($fecha)->toFormattedDateString();
            $diario->save();    
          }

          DB::commit();
    
          Session::flash('success', 'Informe de Caja general ha sido aprobado y se cotabiliza deposito al banco!');
          return redirect()->route('diariocajas.index');
        }
        
        Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
        return back()->withInput()->withErrors($validation);

      } catch (\Exception $e) {
        DB::rollback();
        Session::flash('warning', ' Ocurrio un error en DiariocajasController.update, la transaccion ha sido cancelada!');

        return back();
      }
    }

}
