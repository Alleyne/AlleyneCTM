<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\library\Pant;
use Input, Session, Carbon\Carbon;
use Validator, DB;
use Jenssegers\Date\Date;

use App\Bitacora;
use App\Hash;
use App\Ctdasm;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Un;
use App\Pcontable;
use App\Detalledescuento;

class CtdasmsController extends Controller {
    
  public function __construct()
  {
      $this->middleware('hasAccess');    
  }
    
  /***********************************************************************************************************
   * Esta funcion gerera el estado de cuentas de una determinada unidad, puede ser en formato corto y largo
  ************************************************************************************************************/ 
  public function ecuentas($un_id, $tipo) {  
    //dd($un_id, $tipo);
    
    //=== primero ================================================================
    // calcula el total adeudado en cuotas de mantenimiento regulares    
    //============================================================================
    $imps = Ctdasm::where('un_id', $un_id)
                      ->where('pagada', 0)
                      ->select('id','mes_anio','f_vencimiento','importe')
                      ->orderBy('fecha')
                      ->get();    
 
    // modifica el formato de la fecha de vencimiento en la coleccion
    $imps->map(function ($imp) {
        $imp['f_vencimiento'] = Date::parse($imp->f_vencimiento)->toFormattedDateString();
    });

    $total_importe = $imps->sum('importe');
    // dd($total_importe);
    
    
    //=== segundo ================================================================
    // calcula el total adeudado en recargos en cuotas de mantenimiento regulares    
    //============================================================================
    $recs = Ctdasm::where('un_id', $un_id)
                      ->where('recargo_siono', 1) 
                      ->where('recargo_pagado', 0)
                      ->select('id','mes_anio','recargo_siono','recargo_pagado','recargo')
                      ->orderBy('fecha')
                      ->get();
    // dd($recs->toArray());
    
    $total_recargo = $recs->sum('recargo');   
    // dd($total_recargo);
    
    //=== tercero ================================================================
    // calcula el total en cuotas extraordinarias    
    //============================================================================
    $extras = Ctdasm::where('un_id', $un_id)
                      ->where('extra_siono', 1) 
                      ->where('extra_pagada', 0)
                      ->select('id','mes_anio','extra_siono','extra_pagada','extra')
                      ->orderBy('fecha')
                      ->get();    

    $total_extra = $extras->sum('extra'); 
    //dd($totalAnts);    

    //=== cuarto =================================================================
    // calcula el total de pagos anticipados para obtener descuento    
    //============================================================================
    $ants = Detalledescuento::where('un_id', $un_id)
                      ->where('consumido', 0) 
                      ->select('id','detalle','consumido', 'importe', 'descuento')
                      ->orderBy('fecha')
                      ->get();    

    // agrega un nuevo elemento llamado montoCuota a la coleccion $ants
    $ants->map(function ($ant) {
        $ant['montoCuota'] = ($ant->importe + $ant->descuento);
    });
    
    $totalAnts = $ants->sum('importe') + $ants->sum('descuento'); 
    //dd($totalAnts);

     // Obtiene el primer propietario encargado de la unidad  
    $prop = Prop::where('un_id', $un_id)
                  ->where('encargado', '1')
                  ->with('user')
                  ->first();
    //dd($prop); 

    if(is_null($prop))  {
      Session::flash('warning', 'La Unidad No. '. $un_id . ' selecciona no tiene propietario encargado asignado...');
      return back();
    } 
                  
    // Encuentra los datos de la unidad
    $un = Un::find($un_id);
    // dd($un->toArray());

    // Encuentra los datos de la sección a la cual pertenece la unidad
    $seccion = Seccione::find($un->seccione_id);
    // dd($seccion->toArray());
    
    // Encuentra los datos del Ph al que pertenece la unidad
    $ph = $seccion->bloque->jd;
    //dd($ph->toArray()); 
    
    // encuentra el periodo mas antiguo abierto
    $periodo= Pcontable::where('cerrado',0)->orderBy('id')->first();

    // Encuentra saldo pagados por anticipado
    $pagos_anticipados = Pant::getSaldoCtaPagosAnticipados($un_id, $periodo->id);
    // dd($pagos_anticipados);

    // calcula el total bruto adeudado
    $total = $total_importe + $total_recargo + $total_extra;
    // dd($total_importe, $total_recargo, $total_extra);
    
    // calcula el total neto adeudado
    $total_adeudado = $total - $pagos_anticipados;      
   
    if ($total_adeudado < 0) {
      $total_adeudado = 0;
    }

    // Prepara datos del encabezado del Estado de cuenta
    $data = [
      'propnombre'    => $prop->user->nombre_completo,          
      'propdireccion'   => $prop->user->direccion,
      'propprovincia'   => $prop->user->provincia,
      'propdistrito'    => $prop->user->distrito,
      'propcorregimiento' => $prop->user->corregimiento,
      'proppais' => $prop->user->pais,
      'proptelefono' => $prop->user->telefono,      
      
      'un_id'       => $un->id, 
      'activa'      => $un->activa,
      'codigo'      => $un->codigofull, 
      'fecha'       => Date::today()->format('l\, j \d\e F Y'),
      'total'       => number_format($total, 2),
      'pagos_anticipados'   => number_format($pagos_anticipados, 2),
      'anticipado'          => $pagos_anticipados,
      'total_adeudado'      => 'B/. '.number_format($total_adeudado, 2)
    ];
       
    //dd($imps->toArray(), $recs->toArray(), $total_importe, $total_recargo, $data);
    
    if ($tipo == 'frontend') {
      return view('contabilidad.ctdasms.ecuentasFrontend')
            ->with('data', $data)
            ->with('imps', $imps)
            ->with('recs', $recs)
            ->with('extras', $extras)
            ->with('ants', $ants);
    }
    
    elseif ($tipo == 'backend') {
      return view('contabilidad.ctdasms.ecuentasBackend')
            ->with('data', $data)
            ->with('imps', $imps)
            ->with('recs', $recs)
            ->with('extras', $extras)
            ->with('ants', $ants);
    }
  }
}