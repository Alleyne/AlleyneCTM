<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model 
{
	protected $table = 'facturas';
  public $timestamps = true;    

  public function detallefacturas()
  {
	 return $this->hasMany('App\Detallefactura');    
  }

  public function detallepagofacturas()
  {
	 return $this->hasMany('App\Detallepagofactura');    
  }

  public function org()
  {
    return $this->belongsTo('App\Org');
  }
}