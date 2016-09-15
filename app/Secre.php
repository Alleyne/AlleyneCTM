<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Secre extends Model 
{
	protected $table = 'secres';
    public $timestamps = false;    
   
     public function seccione()
    {
        return $this->belongsTo('App\Seccione');
    }
}