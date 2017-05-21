<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Dte_desembolso extends Model 
{
	protected $table = 'dte_desembolsos';
  public $timestamps = true;    

  public function desembolso()
  {
    return $this->belongsTo('App\Desembolso');
  }
}