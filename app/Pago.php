<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model 
{
  protected $table = 'pagos';
  public $timestamps = false;

  public function detallepagos()
  {
   return $this->hasMany('App\Detallepago');    
  }

  public function ctdasms()
  {
   return $this->hasMany('App\Ctdasm');    
  }   
}