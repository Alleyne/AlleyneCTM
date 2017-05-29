<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Dte_concilia extends Model 
{
	protected $table = 'dte_concilias';
  public $timestamps = true;    

  public function concilia()
  {
    return $this->belongsTo('App\Concilia');
  }
}