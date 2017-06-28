<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Eventodevolucione extends Model 
{
	protected $table = 'eventodevoluciones';
	public $timestamps = true;    
  
  
  public function calendarevento()
  {
  	return $this->belongsTo('App\Calendarevento');
  }

}