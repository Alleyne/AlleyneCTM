<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Ph extends Model 
{
	protected $table = 'phs';
  public $timestamps = true;    
  
  public function secciones()
  {
	 return $this->hasMany('App\Seccione');    
  }
}