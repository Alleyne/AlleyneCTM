<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Dte_cajachica extends Model 
{
	protected $table = 'dte_cajachicas';
  public $timestamps = true;    

  public function cajachica()
  {
    return $this->belongsTo('App\Cajachica');
  }
}