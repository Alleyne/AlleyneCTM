<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Ecajachica extends Model 
{
	protected $table = 'ecajachicas';
    public $timestamps = false;    

    public function dte_ecajachicas()
    {
  	 return $this->hasMany('App\Dte_ecajachica');    
    }
    
    public function org()
    {
      return $this->belongsTo('App\Org');
    }
}