<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Un extends Model 
{
	protected $table = 'uns';
  public $timestamps = true;    
 
  public function seccione() {
      return $this->belongsTo('App\Seccione');
  }
  
  public function props()
  {
      return $this->hasMany('App\Prop');    
  }
  
  public function ctdasms()
  {
      return $this->hasMany('App\Ctdasm');    
  }
}