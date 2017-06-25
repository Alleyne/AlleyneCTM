<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Calendarevento extends Model 
{
	protected $table = 'calendareventos';
	public $timestamps = true;    
  
  public function un()
  {
  	return $this->belongsTo('App\Un');
  }
  
  public function am()
  {
  	return $this->belongsTo('App\Am');
  }

}