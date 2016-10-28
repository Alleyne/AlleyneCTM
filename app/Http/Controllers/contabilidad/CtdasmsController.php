<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\library\Sity;
use Input, Session, Redirect, Str, Carbon\Carbon;
use Validator, View, DB;
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
    $imps = Ctdasm::where('un_id', $un_id)
                      ->where('pagada', 0)
                      ->select('id','mes_anio','f_vencimiento','importe')
                      ->orderBy('fecha')
                      ->get();    

    $recs = Ctdasm::where('un_id', $un_id)
                      ->where('recargo_siono', 1) 
                      ->where('recargo_pagado', 0)
                      ->select('id','mes_anio','recargo_siono','recargo_pagado','recargo')
                      ->orderBy('fecha')
                      ->get();

    $ants = Detalledescuento::where('un_id', $un_id)
                      ->where('consumido', 0) 
                      ->select('id','detalle','consumido', 'importe', 'descuento')
                      ->orderBy('fecha')
                      ->get();    
    
    $i=0;      
    foreach ($ants as $ant) {
      $ants[$i]['montoCuota']= ($ant->importe + $ant->descuento); 
      $i++;
    }  

    // inicializa los contadores
    $total_importe=0;
    $total_recargo=0;

    foreach ($imps as $imp) {
      $imp->f_vencimiento= Date::parse($imp->f_vencimiento)->toFormattedDateString();

      // Acumula el total de importe a pagar
      if ($imp->pagada==0) {
        $total_importe  = $total_importe + $imp->importe;  
      }
    }       

    foreach ($recs as $rec) {
      // Acumula el total de recargos a pagar
      if ($rec->recargo_siono==1 && $rec->recargo_pagado==0) {
        $total_recargo  = $total_recargo + $rec->recargo;  
      }      
    }     

    // Obtiene todos los propietarios de una determinada unidad que sean encardados.  
    $prop = Prop::where('un_id', $un_id)
                  ->where('encargado', '1')
                  ->with('user')
                  ->first();
    // dd($prop); 

    if(is_null($prop))  {
      Session::flash('warning', 'La Unidad No. '. $un_id . ' selecciona no tiene propietario encargado asignado...');
        return Redirect::back();
    } 
                  
    // Encuentra los datos de la unidad
    $un = Un::find($un_id);
    // dd($un->toArray());

    // Encuentra los datos de la sección a la cual pertenece la unidad
    $seccion = Seccione::find($un->seccione_id);
    // dd($seccion->toArray());
    
    // Encuentra los datos del Ph al que pertenece la unidad
    $ph = Ph::find($seccion->ph_id);
    // dd($ph->toArray()); 
    
    // Encuentra saldo pagados por anticipado
    $pagos_anticipados = Sity::getSaldoCtaPagosAnticipados($un_id, Null);
    $total = number_format(($total_importe + $total_recargo), 2);
    
    if ($total == 0) {
      $total_adeudado=(number_format(0, 2));
    }
    else {
      $total_adeudado = number_format($total-$pagos_anticipados, 2);      
      if ($total_adeudado<0) {
        $total_adeudado='0.00';
      }
    }

    // Prepara datos del encabezado del Estado de cuenta
   
    $data = [
      'Titulo'    => 'Bienvenido al ctmaster.net',
      'Contenido'   => 'Contenido del email',
      
      'phlogo'    => $ph->logo,
      'phnombre'    => $ph->nombre,
      'phcalle'   => $ph->calle,
      'phlote'    => $ph->lote,
      'phdistrito'  => $ph->distrito,
      'phprovincia' => $ph->provincia,
      'phtelefono'  => $ph->telefono,
      'phemail'   => $ph->email,          
      
      'propnombre'    => $prop->user->nombre_completo,          
      'propprovincia'   => $prop->user->provincia,
      'propdistrito'    => $prop->user->distrito,
      'propcorregimiento' => $prop->user->corregimiento,
      
      'un_id'      => $un->id, 
      'codigo'      => $un->codigofull, 
      'fecha'       => Date::today()->format('l\, j \d\e F Y'),
      'total'  => 'B/. ' . $total,
      'pagos_anticipados'  => 'B/. ' . number_format($pagos_anticipados, 2),
      'anticipado' => $pagos_anticipados,
      'total_adeudado'  => 'B/. ' . number_format($total_adeudado, 2),
    ];
       
    //dd($imps->toArray(),$recs->toArray(),$total_importe,$total_recargo);
    
    if ($tipo == 'corto') {
      return view('contabilidad.ctdasms.ecuentasCorto')
              ->with('data', $data);
    }
    elseif ($tipo == 'completo') {
      return view('contabilidad.ctdasms.ecuentasCompleto')
            ->with('data', $data)
            ->with('imps', $imps)
            ->with('recs', $recs)
            ->with('ants', $ants);
    }
  }
}