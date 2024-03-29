<?php namespace App\library;
use DB;
use Session;

class Lim {
  /****************************************************************************************
  * Esta function limpia todas las tablas que que tienen relacion con la contabilidad
  *****************************************************************************************/
  public static function limpiar()
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    DB::table('cajachicas')->truncate();
    DB::table('dte_cajachicas')->truncate();

    DB::table('ctdasms')->truncate(); 
    DB::table('ctdasmhis')->truncate(); 
    DB::table('detallepagos')->truncate();
    DB::table('pagos')->truncate();
    DB::table('detallepagofacturas')->truncate();
    DB::table('ctmayores')->truncate();
    DB::table('ctmayorehis')->truncate();   
    DB::table('ctdiarios')->truncate();
    DB::table('ctdiariohis')->truncate(); 
    DB::table('facturas')->truncate();
    DB::table('detallefacturas')->truncate();
    DB::table('pcontables')->truncate();
    DB::table('bitacoras')->truncate();   

    DB::table('detalledescuentos')->truncate();  
    DB::table('diariocajas')->truncate();  
    
    DB::table('ecajachicas')->truncate();
    DB::table('dte_ecajachicas')->truncate();
    
    DB::table('desembolsos')->truncate();
    DB::table('dte_desembolsos')->truncate();
    
    DB::table('pagosnoids')->truncate();

    DB::table('concilias')->truncate();
    DB::table('dte_concilias')->truncate();

    DB::table('calendareventos')->truncate();
    DB::table('eventodevoluciones')->truncate();

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
} //fin de Class Lim