<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Serviproducto extends Model 
{
	protected $table = 'serviproductos';
  public $timestamps = true;    

  public function catalogo()
  {
    return $this->belongsTo('App\Catalogo');
  }

  // un serviproducto pertenece a una o muchas organizaciones
  public function orgs()
  {
    return $this->belongsToMany('App\Org');
  }

  public function dte_ecajachicas()
  {
    return $this->hasMany('App\Dte_ecajachica');
  }

  public function detallefacturas()
  {
    return $this->hasMany('App\Detallefactura');
  }

}