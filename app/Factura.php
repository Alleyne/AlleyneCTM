<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model 
{
	protected $table = 'facturas';
    public $timestamps = false;    

    public function detallefacturas()
    {
  	 return $this->hasMany('App\Detallefactura');    
    }

    public function detallepagofacturas()
    {
  	 return $this->hasMany('App\Detallepagofactura');    
    }
}