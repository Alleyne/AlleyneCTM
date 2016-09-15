<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\library\Sity;
use Input, Session, Redirect, Str, Carbon\Carbon, URL;
use Validator, View;
use Debugbar;

use App\Bitacora;
use App\Hash;
use App\Ctdasm;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Un;
use App\Pcontable;
use Jenssegers\Date\Date;

class CtdasmsController extends Controller {
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }
    
  /***********************************************************************************************************
   * Esta funcion gerera el estado de cuentas de una determinada unidad, puede ser en formato corto y largo
  ************************************************************************************************************/ 
  public function ecuentas($un_id, $tipo) {  
    $imps = Ctdasm::where('un_id', $un_id)
                      ->where('pagada', 0)
                      ->select('id','mes_anio','f_vencimiento','importe')
                      ->get();    

    $recs = Ctdasm::where('un_id', $un_id)
                      ->where('recargo_siono', 1) 
                      ->where('recargo_pagado', 0)
                      ->select('id','mes_anio','recargo_siono','recargo_pagado','recargo')
                      ->get();

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
    
    //encuentra el periodo contable mas antiguo que no este cerrado en donde se encuentras los saldos acutales
    $periodo= Pcontable::where('cerrado', 0)
                       ->orderBy('id')
                       ->first()->id;
    //dd($periodo);

    // Encuentra saldo pagados por anticipado
    $pagos_anticipados = Sity::getSaldoCtaPagosAnticipados($un_id, $periodo);
    $total=number_format(($total_importe + $total_recargo), 2);
    
    if ($total==0) {
      $total_adeudado=(number_format(0, 2));
    }
    else {
      $total_adeudado=abs(number_format($total-$pagos_anticipados, 2));      
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
      'total_adeudado'  => 'B/. ' . number_format($total_adeudado, 2),
    ];
    
    /*---------------------------------------------------------------
    Mail::send('emails.email', $data, function($message) use ($user)
    {
      $message->to($user->email, $user->name)
              ->subject('Welcome to Cribbb!');
    });
    ---------------------------------------------------------------*/
    //dd($prop->user->email);
    //dd($prop->user->nombre_completo);
    
    // Envia Estado de cuenta mediante email a cada uno de los propietarios encargados de una unidad  
    /*Mail::queue('emails.estado_de_cuenta', array($data, $das), function($message) use ($prop)
    {
      $message->to($prop->user->email, $prop->user->nombre_completo)
          ->subject('Welcome to Sityweb mail.Sityweb.net!');
    });*/
        
    //dd($imps->toArray(),$recs->toArray(),$total_importe,$total_recargo);
    if ($tipo == 'corto') {
      return view('contabilidad.ctdasms.ecuentasCorto')
              ->with('data', $data);
    }
    elseif ($tipo == 'completo') {
      return view('contabilidad.ctdasms.ecuentasCompleto')
            ->with('data', $data)
            ->with('imps', $imps)
            ->with('recs', $recs);
    }
  }
}