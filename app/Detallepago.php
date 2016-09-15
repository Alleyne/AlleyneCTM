<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Detallepago extends Model 
{
	protected $table = 'detallepagos';
    public $timestamps = false;    
   
    public function pago() {
        return $this->belongsTo('App\Pago');
    }
}