<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Detallepagofactura extends Model 
{
	protected $table = 'detallepagofacturas';
  public $timestamps = true;    
 
  public function pago() {
    return $this->belongsTo('App\Factura');
  }

  public function Trantipo() {
    return $this->belongsTo('App\Trantipo');
  }
}