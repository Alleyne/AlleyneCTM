<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Seccione extends Model 
{
	protected $table = 'secciones';
    public $timestamps = false;    
   
    public function bloque() {
        return $this->belongsTo('App\Bloque');
    }
    
    public function ph() {
        return $this->belongsTo('App\Ph');
    }    
    
	public function secapto()
    {
        return $this->hasOne('App\Secapto');
    }

    public function uns()
    {
     return $this->hasMany('App\Un');    
    }

    public function secre()
    {
        return $this->hasOne('App\Secre');
    }
/*
    public function seclced()
    {
        return $this->hasOne('App\Seclced');
    }

    public function seclcre()
    {
        return $this->hasOne('App\Seclcre');
    }

    public function ams()
    {
        return $this->hasMany('App\Am');
    }

    public function ess()
    {
        return $this->hasMany('App\Es');
    }*/
    
}