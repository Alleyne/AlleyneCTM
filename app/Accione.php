<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Accione extends Model
{
	protected $table = 'acciones';
    public $timestamps = false;
    
    public function bitacoras()
    {
		return $this->hasMany('App\Bitacora');    
    }   
}