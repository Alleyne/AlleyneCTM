<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Pagosnoid extends Model 
{
	protected $table = 'pagosnoids';
  public $timestamps = true;    

  public function un() {
    return $this->belongsTo('App\Un');
  }

}