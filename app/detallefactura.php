<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Detallefactura extends Model 
{
	protected $table = 'detallefacturas';
  public $timestamps = true;    
  
  public function factura() {
      return $this->belongsTo('App\Factura');
  }

  public function catalogo() {
      return $this->belongsTo('App\Catalogo');
  }
}