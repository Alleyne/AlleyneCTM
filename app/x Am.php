<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Am extends Model 
{
	protected $table = 'ams';
  public $timestamps = true;    
 
   public function seccione()
  {
    return $this->belongsTo('App\Seccione');
  }
}