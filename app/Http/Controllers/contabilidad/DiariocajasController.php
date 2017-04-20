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

      // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque,Efectivo y tarjetas de credito)
      $ingresoEfectivos= Pago::where('f_pago', $fecha)
                ->where(function($query){
                          return $query
                            ->where('trantipo_id', 1)
                            ->orWhere('trantipo_id', 5)
                            ->orWhere('trantipo_id', 6)
                            ->orWhere('trantipo_id', 7)
                            ->orWhere('trantipo_id', 8)
                            ->orWhere('trantipo_id', 9);
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
      $totalIngresoEfectivos= $ingresoEfectivos->sum('monto');  
      //dd($totalIngresoEfectivos); 
      
      // calcula el total de ingresos recibidos por efectivo solamente
      $totalEfectivos= $ingresoEfectivos->where('trantipo_id', 5)->sum('monto');  
      //dd($totalEfectivos);
      
      // calcula el total de ingresos recibidos por cheque solamente
      $totalCheques= $ingresoEfectivos->where('trantipo_id', 1)->sum('monto');  
      //dd($totalCheques);

      // calcula el total de ingresos recibidos por tarjetas clave
      $totalClaves= $ingresoEfectivos->Where('trantipo_id', 6)->sum('monto');  
      //dd($totalClaves);  

      // calcula el total de ingresos recibidos por tarjetas de credito solamente
      $totalTarjetas= $ingresoEfectivos->Where('trantipo_id', 7)->sum('monto');       
      //dd($totalTarjetas);      

      // encuentra los datos para la seccion de Ingresos de efectivo del Informe de caja diario (Cheque y Efectivo)
      // 1. encuentra la cantidad de efectivo desembolsado
      $desembolsoEfectivos= Detallepagofactura::where('detallepagofacturas.fecha', $fecha)
                ->where(function($query){
                                          return $query
                                            ->where('trantipo_id', 1)
                                            ->orwhere('trantipo_id', 5);
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
                ->get(['detallepagofacturas.id as pagoNo','trantipos.id as trantipo_id', 'trantipos.nombre as trantipo','codigo','ctmayores.detalle','credito as monto']);
      //dd($desembolsoEfectivos->toArray()); 

      // calcula el total desembolsado en efectivo solamente      
      $totalDesembolsoEfectivos= $desembolsoEfectivos->sum('monto');  
      //dd($totalDesembolsoEfectivos);

      $fecha= Date::parse($fecha)->toFormattedDateString();

      return view('contabilidad.diariocajas.show')->with('ingresoEfectivos', $ingresoEfectivos)
                                                  ->with('totalEfectivos', $totalEfectivos)
                                                  ->with('totalCheques', $totalCheques)
                                                  ->with('totalClaves', $totalClaves)
                                                  ->with('totalTarjetas', $totalTarjetas)                                                  
                                                  ->with('totalIngresoEfectivos', $totalIngresoEfectivos)                                                  
                                                  
                                                  ->with('desembolsoEfectivos', $desembolsoEfectivos)                                                  
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
