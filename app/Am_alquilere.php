<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Am_alquilere extends Model
{
	protected $table = 'am_alquileres';
  public $timestamps = true;

  public function calendareventos()
  {
      return $this->hasMany('App\Calendarevento');    
  }

}