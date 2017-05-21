<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Blqadmin extends Model 
{
	protected $table = 'blqadmins';
    public $timestamps = true;    
    
    public function bloque() {
        return $this->belongsTo('App\Bloque');
    }

    // un administrador es un usuario
    public function user() {
        return $this->belongsTo('App\User');
    }

    // un administrador puede o no pertenecer a una organizacion
    public function org() {
        return $this->belongsTo('App\Org');
    }
}