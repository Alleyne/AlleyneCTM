<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Trantipo extends Model 
{
	protected $table = 'trantipos';
  public $timestamps = true;    
  
  public function pagos()
  {
		return $this->hasMany('App\Pago');    
  }
}