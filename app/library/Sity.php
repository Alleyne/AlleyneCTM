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
use Mail;
use App\Mail\sendnuevoEcuentas;
use DB;
use App\Notifications\emailNuevaOcobro;
use App\Notifications\emailUsoDeCuentaAnticipados;

use App\Bitacora;
use App\User;
use App\Un;
use App\Ctdasm;
use App\Blqadmin;
use App\Ctdiario;
use App\Ctdiariohi;
use App\Detallepago;
use App\Pago;
use App\Ctmayore;
use App\Ctmayorehi;
use App\Pcontable;
use App\Catalogo;
use App\Ht;
use App\Detalledescuento;
use App\Prop;
use App\Seccione;
use App\Ph;
use App\Secapto;

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
   * Registra en ctmayores
   ****************************************************************************************/
  public static function registraEnCuentas($pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id=Null, $pago_id=Null, $detallepagofactura_id=Null, $org_id=Null, $ctdasm_id=Null, $anula=Null)
  {
    //dd($pcontable_id, $mas_menos, $tipo, $cuenta, $fecha, $detalle, $monto, $un_id=Null, $pago_id=Null, $detallepagofactura_id, $org_id=Null, $ctdasm_id=Null, $anula=Null);
    
    $debito = 0;
    $credito = 0;
    //dd($cuenta);

    // encuentra las generales de la cuenta
    $cta = Catalogo::find($cuenta);
    //dd($cta->toArray());      
  
    // encuentra saldos
    $saldocta= Sity::getSaldoCuenta($cuenta, $pcontable_id);

    // determina si los saldos aumentan o disminuyen en cuentas permanentes
    if (($tipo==1 || $tipo==6) && $mas_menos=='mas') {  
      //$saldocta= $saldocta+$monto;
      $debito = $monto;
      
    } elseif (($tipo==1 || $tipo==6) && $mas_menos=='menos') {  
      //$saldocta= $saldocta-$monto;
      $credito =abs($monto);
    
    // determina si los saldos aumentan o disminuyen en cuentas temporales
    } elseif (($tipo==2 || $tipo==3 || $tipo==4) && $mas_menos=='mas') {  
      //$saldocta= $saldocta+$monto;
      $credito =$monto;    

    } elseif (($tipo==2 || $tipo==3 || $tipo==4) && $mas_menos=='menos') {  
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
    $dato->un_id                  = $un_id;
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
              $monthName='Ene';
              break;
          case 2:
              $monthName='Feb';
              break;
          case 3:
              $monthName='Mar';
              break;
          case 4:
              $monthName='Abr';
              break;
          case 5:
              $monthName='May';
              break;
          case 6:
              $monthName='Jun';
              break;       
         case 7:
              $monthName='Jul';
              break;
          case 8:
              $monthName='Ago';
              break;
          case 9:
              $monthName='Sep';
              break;
          case 10:
              $monthName='Oct';
              break;
          case 11:
              $monthName='Nov';
              break;
          case 12:
              $monthName='Dic';
              break;       
      }
  return $monthName;
  }
 
  /*****************************************************************************************
   *  Registra en Bitacoras.
   *****************************************************************************************/
  public static function RegistrarEnBitacora($accione_id, $tabla, $registro=Null, $detalle) {
      $bitacora = new Bitacora;
      $bitacora->fecha           = Carbon::today();       
      $bitacora->hora            = Carbon::now('America/Panama');
      $bitacora->accione_id      = $accione_id;
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

/****************************************************************************************
 * Esta function registra en Ctdiario principal el resumen de la facturacion del mes
 *****************************************************************************************/
public static function xxxregistraRecargosEnCtdiario($recargo, $periodo_id, $ocobro)
{
       
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->fecha   = Carbon::today();
    $dato->detalle = 'Recargo por cobrar en cuota de mantenimiento';
    $dato->debito  = $recargo;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->detalle = '   Ingresos por cuota de mantenimiento';
    $dato->credito = $recargo;
    $dato->save(); 
    
    // registra en Ctdiario principal
    $dato = new Ctdiario;
    $dato->pcontable_id  = $periodo_id;
    $dato->detalle = 'Para registrar recargo en cuotas de mantenimiento de la unidad '.$ocobro;
    $dato->save(); 
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



} //fin de Sity