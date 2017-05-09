<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
 	protected $table = 'bitacoras';
  public $timestamps = false;
 	
  public function users()
  {
  	return $this->belongsTo('App\User');
  }
}