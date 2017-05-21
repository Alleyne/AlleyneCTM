<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Desembolso extends Model 
{
	protected $table = 'desembolsos';
    public $timestamps = true;    

    public function dte_desembolsos()
    {
  	 return $this->hasMany('App\Dte_desembolso');    
    }

    public function cajachica()
    {
    	return $this->belongsTo('App\Cajachica');
    }
}