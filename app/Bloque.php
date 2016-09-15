<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Bloque extends Model 
{
	protected $table = 'bloques';
    public $timestamps = false;    
    
    public function secciones()
    {
  	 return $this->hasMany('App\Seccione');    
    }
    
    public function blqadmins()
    {
  	 return $this->hasMany('App\blqadmin');    
    }

    public function jd() {
        return $this->belongsTo('App\Jd');
    }
}