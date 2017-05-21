<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Ctmayore extends Model 
{
	protected $table = 'ctmayores';
  public $timestamps = true;    

  public function pago()
  {
      return $this->belongsTo('App\Pago');
  }

}