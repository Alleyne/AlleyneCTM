<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Am extends Model
{
	protected $table = 'ams';
  public $timestamps = true;

  public function calendareventos()
  {
      return $this->hasMany('App\Calendarevento');    
  }

}