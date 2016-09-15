<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Detallepagofactura extends Model 
{
	protected $table = 'detallepagofacturas';
    public $timestamps = false;    
   
    public function pago() {
        return $this->belongsTo('App\Factura');
    }
}