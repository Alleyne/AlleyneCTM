<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Dte_ecajachica extends Model 
{
	protected $table = 'dte_ecajachicas';
  public $timestamps = false;    

  public function ecajachica()
  {
    return $this->belongsTo('App\Ecajachica');
  }
}