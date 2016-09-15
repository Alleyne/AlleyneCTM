<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Ctdasm extends Model 
{
	protected $table = 'ctdasms';
    public $timestamps = true;    
 
    public function un() {
    	return $this->belongsTo('App\Un');
    }   
    
    public function pago() {
    	return $this->belongsTo('App\Pago');
    }   
}