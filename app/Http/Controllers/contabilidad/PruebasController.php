<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\library\Lim;
use Session, DB;

use App\Un;

class PruebasController extends Controller {
  
    public function __construct()
    {
        $this->middleware('auth');  
    }
 
    /*************************************************************************************
     * 
     ************************************************************************************/  
    public function lim()
    {
        Un::where('inicializada', 1)
          ->update(['inicializada' => 0]);  
        Lim::limpiar();
        
        Session::flash('success', 'Las tablas han sido limpiadas!');
        return redirect()->route('backend');
    }

    /*************************************************************************************
     * 
     ************************************************************************************/  
/*    public function bbb()
    {
        Un::where('inicializada', 1)
          ->update(['inicializada' => 0]);
        return 'Se inicializa todas las unidades a cero ...';
    }    */    


    /*************************************************************************************
     * 
     ************************************************************************************/  
    public function limcore()
    {
       
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('props')->truncate();
        DB::table('uns')->truncate();
        DB::table('secciones')->truncate();
        DB::table('secaptos')->truncate();
        DB::table('phs')->truncate();
        DB::table('bloques')->truncate();
        DB::table('blqadmins')->truncate();
        DB::table('jds')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return 'tablas limpias';
    }
}