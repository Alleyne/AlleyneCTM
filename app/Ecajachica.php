<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Ecajachica extends Model 
{
	protected $table = 'ecajachicas';
    public $timestamps = false;    

    public function detallefacturas()
    {
  	 return $this->hasMany('App\dte_ecajachica');    
    }
}