<?php

namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

use App\Http\Requests;
use App\Diariocaja;
use App\Pago;
use App\Ctmayore;
use App\Detallepagofactura;
use Session;

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

      // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque y Efectivo)
      $ingresoEfectivos= Pago::where('f_pago', $fecha)
                ->where(function($query){
                                          return $query
                                            ->where('trantipo_id', 1)
                                            ->orWhere('trantipo_id', 5);
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
                ->get(['pagos.id as pagoNo', 'trantipos.nombre','codigo','detalle','debito as monto']);
      //dd($ingresoEfectivos->toArray()); 
      
      // calcula el total de ingresos recibidos por efectivo y cheques
      $totalIngresoEfectivos= $ingresoEfectivos->sum('monto');  
      //dd($totalIngresoEfectivos); 
      
      // calcula el total de ingresos recibidos por efectivo solamente
      $totalEfectivos= $ingresoEfectivos->where('nombre', 'Efectivo')->sum('monto');  
      //dd($totalEfectivos);
      
      // calcula el total de ingresos recibidos por cheque solamente
      $totalCheques= $ingresoEfectivos->where('nombre', 'Cheque')->sum('monto');  
      //dd($totalCheques);

      // encuentra los datos para la seccion de ingresos por tarjetas del Informe de caja diario (Tarjetas clave, Visa, Master Card y American Express)
      $ingresoTarjetas= Pago::where('f_pago', $fecha)
                ->where(function($query){
                                          return $query
                                            ->where('trantipo_id', 6)
                                            ->orwhere('trantipo_id', 7)
                                            ->orwhere('trantipo_id', 8)
                                            ->orwhere('trantipo_id', 9);
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
                ->get(['pagos.id as pagoNo', 'trantipos.nombre','codigo','detalle','debito as monto']);
      //dd($ingresoTarjetas->toArray());

      // calcula el total de ingresos recibidos por efectivo y cheques      
      $totalIngresosTarjetas= $ingresoTarjetas->sum('monto');  
      //dd($totalIngresosTarjetas);

      // calcula el total de ingresos recibidos por tarjeta Visa
      $totalClave= $ingresoTarjetas->where('nombre', 'Tarjeta Clave')->sum('monto');  
      //dd($totalClave);

      // calcula el total de ingresos recibidos por tarjeta Visa
      $totalVisa= $ingresoTarjetas->where('nombre', 'Visa')->sum('monto');  
      //dd($totalVisa);

      // calcula el total de ingresos recibidos por Master Card
      $totalMasterCard= $ingresoTarjetas->where('nombre', 'Master Card')->sum('monto');  
      //dd($totalMasterCard);

      // calcula el total de ingresos recibidos por tarjeta American Express
      $totalAmericanExpress= $ingresoTarjetas->where('nombre', 'American Express')->sum('monto');  
      //dd($totalAmericanExpress);

      // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque y Efectivo)
      // 1. encuentra la cantidad de efectivo desembolsado
      $desembolsoEfectivos= Detallepagofactura::where('detallepagofacturas.fecha', $fecha)
                ->where(function($query){
                                          return $query
                                            ->where('trantipo_id', 5);
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
                ->get(['detallepagofacturas.id as pagoNo', 'trantipos.nombre','codigo','ctmayores.detalle','credito as monto']);
      //dd($desembolsoEfectivos->toArray()); 

      // calcula el total desembolsado en efectivo solamente      
      $totalDesembolsoEfectivos= $desembolsoEfectivos->sum('monto');  
      //dd($totalDesembolsoEfectivos);

      $fecha= Date::parse($fecha)->toFormattedDateString();

      return view('contabilidad.diariocajas.show')->with('ingresoEfectivos', $ingresoEfectivos)
                                                  ->with('totalIngresoEfectivos', $totalIngresoEfectivos)
                                                  ->with('totalEfectivos', $totalEfectivos)
                                                  ->with('totalCheques', $totalCheques)
                                                  
                                                  ->with('desembolsoEfectivos', $desembolsoEfectivos)                                                  
                                                  ->with('totalDesembolsoEfectivos', $totalDesembolsoEfectivos)

                                                  ->with('ingresoTarjetas', $ingresoTarjetas)                                                  
                                                  ->with('totalIngresosTarjetas', $totalIngresosTarjetas)
                                                  ->with('totalClave', $totalClave)
                                                  ->with('totalVisa', $totalVisa)
                                                  ->with('totalMasterCard', $totalMasterCard)
                                                  ->with('totalAmericanExpress', $totalAmericanExpress)
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

}
