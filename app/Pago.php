<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model 
{
  protected $table = 'pagos';
  public $timestamps = true;

  public function detallepagos()
  {
   return $this->hasMany('App\Detallepago');    
  }

  public function ctdasms()
  {
   return $this->hasMany('App\Ctdasm');    
  }   
  
  public function banco()
  {
      return $this->belongsTo('App\Banco');
  }

  public function un()
  {
      return $this->belongsTo('App\Un');
  }
  
  public function trantipo()
  {
      return $this->belongsTo('App\Trantipo');
  }

  public function ctmayores()
  {
   return $this->hasMany('App\Ctmayore');    
  }

}