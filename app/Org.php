<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Org extends Model 
{
	protected $table = 'orgs';
    public $timestamps = false;
   	
    // una organizacion puede tener uno o muchos administradores de bloques
    public function blqadmins() {
        return $this->hasMany('App\blqadmin');
    }
    
    // una organizacion puede tener uno o muchos usuarios vinculados,
    // los usuarios que esten vinculados a una organizacion tendran permiso para
    // accesar al sistema y ver informacion concerniente a la organizacion que representan
    public function users() {
        return $this->hasMany('App\user');
    }
    
    // una organizacion puede tener asignadas uno o muchos serviprodustos
    public function serviproductos()
    {
        return $this->belongsToMany('App\Serviproducto');
    }

    // una organizacion puede tener una o muchas facturas
    public function facturas() {
        return $this->hasMany('App\factura');
    }

    // una organizacion puede estar en una o muchas ecajachica
    public function ecajachicas() {
        return $this->hasMany('App\Ecajachica');
    }
}