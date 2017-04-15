<?php namespace App\library;

use App\Ctmayore;
use Log;

class Pant {

  /** 
  *==================================================================================================
  *  Encuentra el saldo actual de la cuenta de pagos anticipado de una unidad en especial
  * @param  string  $un_id    "1"
  * @param  integer $periodo  3  
  * @return void
  **************************************************************************************************/
  public static function getSaldoCtaPagosAnticipados($un_id, $periodo) {
    //dd($un_id, $periodo);
    $sa= 0;
    
    // encuentra el total de pagos anticipados tomando en cuenta y determinado periodo y los periodo anteriores al mismo
    //if($periodo) {
      $tcredito= Ctmayore::where('un_id', $un_id)
                  ->where('pcontable_id', $periodo)
                  ->where('cuenta', 5)
                  ->sum('credito');
      
      $tdebito= Ctmayore::where('un_id', $un_id)
                  ->where('pcontable_id',$periodo)
                  ->where('cuenta', 5)
                  ->sum('debito');
      //dd($tcredito, $tdebito);        
      
      $sa= round(floatval($tcredito),2) - round(floatval($tdebito),2);
    
    // si no tiene saldo, iniciliza en cero
    $sa = ($sa) ? $sa : 0;
    //dd($sa);    
    return $sa;
  }


} //fin de Class Pant