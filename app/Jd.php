<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Jd extends Model
{
 	protected $table = 'jds';
    public $timestamps = false;
    
    public function bloques() {
        return $this->hasMany('App\Bloque');
    }   	
}