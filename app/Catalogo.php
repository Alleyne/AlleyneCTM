<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model 
{
	protected $table = 'catalogos';
  public $timestamps = true;    

  public function serviproductos()
  {
      return $this->hasMany('App\Serviproducto');
  }
}