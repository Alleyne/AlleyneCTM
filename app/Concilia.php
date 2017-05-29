<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Concilia extends Model 
{
	protected $table = 'concilias';
  public $timestamps = true;    

  public function dte_concilias()
  {
	 return $this->hasMany('App\Dte_concilia');    
  }
}