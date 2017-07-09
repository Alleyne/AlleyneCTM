<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Seccione extends Model 
{
	protected $table = 'secciones';
  public $timestamps = true;    
 
  public function bloque() {
      return $this->belongsTo('App\Bloque');
  }
  
  public function ph() {
      return $this->belongsTo('App\Ph');
  }    
    
	public function secapto()
  {
      return $this->hasOne('App\Secapto');
  }

  public function uns()
  {
   return $this->hasMany('App\Un');    
  }
}