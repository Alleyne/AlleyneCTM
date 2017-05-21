<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Secapto extends Model 
{
	protected $table = 'secaptos';
  public $timestamps = true;    
 
   public function seccione()
  {
    return $this->belongsTo('App\Seccione');
  }
}