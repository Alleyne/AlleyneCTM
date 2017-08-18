<?php namespace App\library;

/*
|--------------------------------------------------------------------------
| Librería de códigos própios del sistema de inventario de Compras
|--------------------------------------------------------------------------
| Desarrollados y probados por:
| Germán Barrios
| sep 12, 2013
*/
use Event;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Input;

use App\Bitacora;
use App\User;
use App\Un;
use App\Ctdasm;
use App\Blqadmin;
use App\Ctmayore;
use App\Catalogo;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Dte_desembolso;
use App\Desembolso;
use App\Bloque;
use App\Ctmayorehi;

class Sity {

  //ejemplo de cómo crear una variable publica a la clase
  //private static $ids = array();
  
  /****************************************************************************************
   * Encuentra el saldo actual de una cuenta de acuerdo al tipo de cuenta y periodo contable
   *****************************************************************************************/
  public static function getSaldoCuenta($cuenta, $pcontable_id)
  {
  
    // encuentra el tipo de cuenta
    $tipo= Catalogo::find($cuenta)->tipo;

    $tdebito= Ctmayore::where('cuenta', $cuenta)
                ->where('pcontable_id', $pcontable_id)
                ->sum('debito');
    $tdebito= round(floatval($tdebito),2);
    
    $tcredito= Ctmayore::where('cuenta', $cuenta)
                ->where('pcontable_id', $pcontable_id)
                ->sum('credito');
    $tcredito= round(floatval($tcredito),2);    
    
    if ($tipo==1 || $tipo==6) {  
      $sa= $tdebito-$tcredito;

    } elseif ($tipo==2 || $tipo==3 || $tipo==4) {  
      $sa= $tcredito-$tdebito;
    
    } else {
      return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
    } 

    // si no tiene saldo, iniciliza en cero
    $sa = ($sa) ? $sa : 0;
    //dd($sa);    
    return $sa;
  }

  
  /****************************************************************************************
   * Encuentra el saldo actual de una cuenta de acuerdo al tipo de cuenta y periodo contable
   *****************************************************************************************/
  public static function getSaldoCuentaHis($cuenta, $pcontable_id)
  {
  
    // encuentra el tipo de cuenta
    $tipo= Catalogo::find($cuenta)->tipo;

    $tdebito= Ctmayorehi::where('cuenta', $cuenta)
                ->where('cierre', 0)
                ->where('pcontable_id', $pcontable_id)
                ->sum('debito');
    $tdebito= round(floatval($tdebito),2);
    
    $tcredito= Ctmayorehi::where('cuenta', $cuenta)
                ->where('cierre', 0)
                ->where('pcontable_id', $pcontable_id)
                ->sum('credito');
    $tcredito= round(floatval($tcredito),2);    
    
    if ($tipo==1 || $tipo==6) {  
      $sa= $tdebito-$tcredito;

    } elseif ($tipo==2 || $tipo==3 || $tipo==4) {  
      $sa= $tcredito-$tdebito;
    
    } else {
      return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
    } 

    // si no tiene saldo, iniciliza en cero
    $sa = ($sa) ? $sa : 0;
    //dd($sa);    
    return $sa;
  }



  /****************************************************************************************
   * Encuentra el saldo actual de una cuenta de acuerdo al tipo de cuenta y periodo contable
   *****************************************************************************************/
  public static function getSaldoCuentaLastPcontable($cuenta, $cuentaTipo)
  {
  
    $lastPcontable = Ctmayorehi::orderBy('pcontable_id', 'desc')->first()->pcontable_id;
    //dd($lastPcontable);

    if ($lastPcontable) {
      $tdebito = Ctmayorehi::where('cuenta', $cuenta)
                  ->where('pcontable_id', $lastPcontable)
                  ->sum('debito');
      $tdebito = round(floatval($tdebito),2);
      
      $tcredito = Ctmayorehi::where('cuenta', $cuenta)
                  ->where('pcontable_id', $lastPcontable)
                  ->sum('credito');
      $tcredito = round(floatval($tcredito),2);    
      
      if ($cuentaTipo == 1 || $cuentaTipo == 6) {  
        $sa= $tdebito - $tcredito;

      } elseif ($cuentaTipo == 2 || $cuentaTipo == 3 || $cuentaTipo ==4) {  
        $sa= $tcredito - $tdebito;
      
      } else {
        return 'Error: tipo de cuenta no exite en function Sity::getSaldoCuenta()';
      } 

      // si no tiene saldo, iniciliza en cero
      $sa = ($sa) ? $sa : 0;
    } else {
      $sa = 0;
    }

    //dd($sa);    
    return $sa;
  }


  /****************************************************************************************
   * Registra en ctmayores
   ****************************************************************************************/
  public static function registraEnCuentas($pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id = Null, $pago_id = Null, $detallepagofactura_id = Null, $org_id = Null, $ctdasm_id = Null, $anula = Null)
  {
    //dd($pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id = Null, $pago_id = Null, $detallepagofactura_id = Null, $org_id = Null, $ctdasm_id = Null, $anula = Null);
    
    $debito = 0;
    $credito = 0;
    //dd($cuenta);

    // encuentra las generales de la cuenta
    $cta = Catalogo::find($cuenta);
    //dd($cta->toArray());      
  
    // encuentra saldos
    $saldocta = Sity::getSaldoCuenta($cuenta, $pcontable_id);

    // determina si los saldos aumentan o disminuyen en cuentas permanentes
    if (($tipo == 1 || $tipo == 6) && $mas_menos == 'mas') {  
      //$saldocta= $saldocta+$monto;
      $debito = $monto;
      
    } elseif (($tipo == 1 || $tipo == 6) && $mas_menos == 'menos') {  
      //$saldocta= $saldocta-$monto;
      $credito = abs($monto);
    
    // determina si los saldos aumentan o disminuyen en cuentas temporales
    } elseif (($tipo == 2 || $tipo == 3 || $tipo == 4) && $mas_menos == 'mas') {  
      //$saldocta= $saldocta+$monto;
      $credito = $monto;    

    } elseif (($tipo == 2 || $tipo == 3 || $tipo == 4) && $mas_menos == 'menos') {  
      //$saldocta= $saldocta-$monto;
      $debito = abs($monto);
    
    } else {
      return 'Error: tipo de cuenta no exite en function Sity::registraEnCuentas()';
    } 

    // agrega el nuevo registro al modelo ctmayore
    $dato = new Ctmayore;
    $dato->pcontable_id           = $pcontable_id;
    $dato->tipo                   = $tipo;
    $dato->cuenta                 = $cuenta;
    $dato->codigo                 = $cta->codigo;
    $dato->fecha                  = $fecha;
    $dato->detalle                = $detalle;
    $dato->debito                 = $debito;
    $dato->credito                = $credito;
    
    if ($un_id) {
      $un = Un::find($un_id);
      $dato->un_id                  = $un_id;
      $dato->seccione_id            = $un->seccione->id;
      $dato->bloque_id              = $un->seccione->bloque_id;
    }
    
    $dato->detallepagofactura_id  = $detallepagofactura_id;
    $dato->org_id                 = $org_id; 
    $dato->pago_id                = $pago_id; 
    $dato->ctdasm_id              = $ctdasm_id;
    $dato->anula                  = $anula;
    $dato->save();
  }   

 
  /*****************************************************************************************
   * Determina el nombre del mes.
   *****************************************************************************************/
  public static function getMonthName($month) {
      switch ($month) {
          case 1:
              $monthName = 'Ene';
              break;
          case 2:
              $monthName = 'Feb';
              break;
          case 3:
              $monthName = 'Mar';
              break;
          case 4:
              $monthName = 'Abr';
              break;
          case 5:
              $monthName = 'May';
              break;
          case 6:
              $monthName = 'Jun';
              break;       
         case 7:
              $monthName = 'Jul';
              break;
          case 8:
              $monthName = 'Ago';
              break;
          case 9:
              $monthName = 'Sep';
              break;
          case 10:
              $monthName = 'Oct';
              break;
          case 11:
              $monthName = 'Nov';
              break;
          case 12:
              $monthName = 'Dic';
              break;       
      }
  return $monthName;
  }
 
  /*****************************************************************************************
  *  Registra en Bitacoras tipo resource store, update y destroy.
  *****************************************************************************************/
  public static function RegistrarEnBitacora($dato, $input = Null, $modelo, $accion) {      
    //dd($dato->isDirty(), $dato, $input);
   
    $attributes = array_keys($dato->toArray());
    $detalle = '';

    if($dato->isDirty()){
      // se trata de un update     
      //dd('se trata de un update', $dato, $input);

      foreach ($attributes as $attribute) {
        if ($dato->isDirty($attribute)) {
          if (Input::get($attribute) != $dato->getOriginal($attribute)) {
            $detalle = $detalle.' "'.strtoupper($attribute).'" cambia de "'.$dato->getOriginal($attribute).'" a "'.Input::get($attribute).'", ';
          }
        }
      }
      
      $tabla = $dato->getTable();
      $registro = $dato->id;
    
    } elseif(!$dato->isDirty() && $input){
      // se trata de un create 
      //dd('se trata de un create', $dato, $input);
      foreach ($attributes as $attribute) {
        if ($dato[$attribute]) {
          $detalle = $detalle.strtoupper($attribute).' => '.$dato[$attribute].', ';
        }
      }

      $tabla = $dato->getTable();
      $modelName = 'App\\'.$modelo;
      $registro = $dato->id;
    
    } elseif(!$dato->isDirty() && $input == Null){
      // se trata de un delete
      //dd('se trata de un delete', $dato, $input);
      foreach ($attributes as $attribute) {
        if ($dato[$attribute]) {
          $detalle = $detalle.strtoupper($attribute).' => '.$dato[$attribute].', ';
        }
      }
      
      $tabla = $dato->getTable();
      $registro = $dato->id;
    
    } else {
      dd('ninguna de las ateriores');      
    }

    $bitacora = new Bitacora;
    $bitacora->fecha           = Carbon::today();       
    $bitacora->hora            = Carbon::now('America/Panama');
    $bitacora->accion          = $accion;
    $bitacora->tabla           = $tabla;
    $bitacora->registro        = $registro;            
    $bitacora->detalle         = $detalle;
    if (Auth::check()) {
        $bitacora->user_id     = Auth::user()->id;            
    }
    $bitacora->ip              = $_SERVER["REMOTE_ADDR"];
    $bitacora->save();
    return 'nada';
  }    

  /*****************************************************************************************
  *  Registra en Bitacoras tipo especial vincula, desvincula, subir imagen
  *****************************************************************************************/
  public static function RegistrarEnBitacoraEsp($detalle, $tabla, $registro, $accion) {      
    //dd($detalle, $tabla, $accion);

    $bitacora = new Bitacora;
    $bitacora->fecha           = Carbon::today();       
    $bitacora->hora            = Carbon::now('America/Panama');
    $bitacora->accion          = $accion;
    $bitacora->tabla           = $tabla;
    $bitacora->registro        = $registro;            
    $bitacora->detalle         = $detalle;
    if (Auth::check()) {
        $bitacora->user_id     = Auth::user()->id;            
    }
    $bitacora->ip              = $_SERVER["REMOTE_ADDR"];
    $bitacora->save();
    return 'nada';
  }  

  /****************************************************************************************
   * Esta function encuentra el user_id del administrador encargado del bloque al cual
   * pertenece la seccion en estudio
   *****************************************************************************************/
  public static function findBlqadmin($bloque_id)
  {
    //Encuentra el administrador encargado del bloque al cual pertenece la seccion
    $bug=Blqadmin::where('encargado', '1')
                ->where('bloque_id', $bloque_id)
                ->first();
    return $bug->user_id;
  }


  /***********************************************************************************
  * Proceso de recoger la data de un estado de cuentas
  ************************************************************************************/ 
  public static function getdataEcuenta($un_id) {
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

    // inicializa los contadores
    $total_importe = 0;
    $total_recargo = 0;

    foreach ($imps as $imp) {
      $imp->f_vencimiento = Date::parse($imp->f_vencimiento)->toFormattedDateString();

      // Acumula el total de importe a pagar
      if ($imp->pagada == 0) {
        $total_importe  = $total_importe + $imp->importe;  
      }
    }       

    foreach ($recs as $rec) {
      // Acumula el total de recargos a pagar
      if ($rec->recargo_siono == 1 && $rec->recargo_pagado == 0) {
        $total_recargo = $total_recargo + $rec->recargo;  
      }      
    }     

    // Obtiene todos los propietarios de una determinada unidad que sean encardados.  
    $prop = Prop::where('un_id', $un_id)
                  ->where('encargado', '1')
                  ->with('user')
                  ->first();
    // dd($prop); 

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
      $total_adeudado = (number_format(0, 2));
    }
    else {
      $total_adeudado = abs(number_format($total-$pagos_anticipados, 2));      
    }

    // Prepara datos del encabezado del Estado de cuenta
   
    $data = [
      'Titulo'    => 'Bienvenido al ctmaster.net',
      'Contenido' => 'Contenido del email',
      
      'phlogo'      => $ph->logo,
      'phnombre'    => $ph->nombre,
      'phcalle'     => $ph->calle,
      'phlote'      => $ph->lote,
      'phdistrito'  => $ph->distrito,
      'phprovincia' => $ph->provincia,
      'phtelefono'  => $ph->telefono,
      'phemail'     => $ph->email,          
      
      'propnombre'        => $prop->user->nombre_completo,          
      'propprovincia'     => $prop->user->provincia,
      'propdistrito'      => $prop->user->distrito,
      'propcorregimiento' => $prop->user->corregimiento,
      
      'un_id'       => $un->id, 
      'codigo'      => $un->codigofull, 
      'fecha'       => Date::today()->format('l\, j \d\e F Y'),
      'total'       => 'B/. ' . $total,
      'pagos_anticipados'   => 'B/. ' . number_format($pagos_anticipados, 2),
      'total_adeudado'      => 'B/. ' . number_format($total_adeudado, 2),
    ];
       
    //dd($imps->toArray(),$recs->toArray(),$total_importe,$total_recargo);
    return array('imps'=>$imps,'recs'=>$recs,'data'=>$data);  
  }


  /****************************************************************************************
  * Verifica si la caja chica en estudio tiene algun desembolso sin aprobar,
  * Si no exite lo crea y le vincula los detalles del presente egreso de caja chica.
  
  * Si existe, entonces actualiza la fecha con la fecha del presente egreso de caja y
  * le vincula los detalles del presente egreso de caja chica.
   *****************************************************************************************/
  public static function analizaDesembolsos($ccchica_id)
  {
 
    //verifica si hay algun desembolso sin aprobar en la presente caja chica
    $desembolso = Desembolso::where('cajachica_id', $ccchica_id)->where('aprobado', 0)->first();
    //dd($desembolso);      
    
    //verifica si hay detalles de desembolsos por asignar
    $dte_desembolso = Dte_desembolso::where('desembolso_id', 0)->first();
    //dd($desembolso, $dte_desembolso, $cchicaCerrada);

    // si no existe ningun desembolso por aprobar, entonces crea un nuevo desembolso
    // true, null, null
    if (is_null($desembolso)) {
      // crea un nuevo desembolso
      $desemb = new Desembolso;
      $desemb->fecha = Carbon::today();
      $desemb->cajachica_id = $ccchica_id;
      $desemb->save();
      
      $desembolso_id = $desemb->id;
    
    } elseif ($desembolso && $dte_desembolso) {
      // actualiza la fecha del desembolos
      $desemb = Desembolso::find($desembolso->id);
      $desemb->fecha = Carbon::today();
      $desemb->save();

      $desembolso_id = $desembolso->id;
    }

    //encuentra todos los detalles de desembolso que tengan desembolso_id igual a cero y se los asigna
    // al recien creado desembolso
    Dte_desembolso::where('desembolso_id', 0)
                  ->update(['desembolso_id' => $desembolso_id]);       
    
    return;
  }

} //fin de Sity