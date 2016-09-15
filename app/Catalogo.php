<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model 
{
	protected $table = 'catalogos';
    public $timestamps = false;    

    public function orgs()
    {
        return $this->belongsToMany('App\Org');
    }

    public function detallefacturas()
    {
        return $this->hasMany('App\detallefactura');
    }
}