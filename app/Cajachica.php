<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Cajachica extends Model 
{
	protected $table = 'cajachicas';
    public $timestamps = true;    

  public function dte_cajachicas()
  {
	 return $this->hasMany('App\Dte_cajachica');    
  }

  public function desembolsos()
  {
	 return $this->hasMany('App\Desembolso');    
  }
}