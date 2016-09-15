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
    
    public function catalogos()
    {
        return $this->belongsToMany('App\Catalogo');
    }
}