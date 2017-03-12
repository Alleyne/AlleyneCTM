<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Dte_cajachica extends Model 
{
	protected $table = 'dte_cajachicas';
  public $timestamps = false;    

  public function ecajachica()
  {
    return $this->belongsTo('App\Ecajachica');
  }
}