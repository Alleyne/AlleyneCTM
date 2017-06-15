<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model 
{
	protected $table = 'eventos';
  public $timestamps = true;    

  protected $fillable = ['fechaIni','fechaFin','todoeldia','lugar','color','titulo'];
  protected $hidden = ['id'];

}