<?php namespace App\library;
use DB;

class Lim {
  /****************************************************************************************
  * Esta function limpia todas las tablas que que tienen relacion con la contabilidad
  *****************************************************************************************/
  public static function limpiar()
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    DB::table('ctdasms')->truncate(); 
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
    DB::table('hts')->truncate();   
    DB::table('detalledescuentos')->truncate();  

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    return 'Tablas han sido limpiadas!';
  }




} //fin de Class Lim