<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Prop extends Model 
{
	use Notifiable;

    protected $table = 'props';
    public $timestamps = false;    
    
    public function uns() {
        return $this->belongsTo('App\Un');
    }

    // un administrador es un usuario
    public function user() {
        return $this->belongsTo('App\User');
    }

    // un administrador puede o no pertenecer a una organizacion
    public function org() {
        return $this->belongsTo('App\Org');
    }

    /*
    * When building JSON APIs, you will often need to convert your models and relationships to arrays or JSON.
    * Eloquent includes convenient methods for making these conversions, as well as controlling which attributes
    * are included in your serializations.
    
    public function getuserAttribute()
    {
        //$jd =Jd::find($this->attributes['jd_id']);
        $user =User::where('id', $this->attributes['user_id'])->get();
        //dd($propietario->toArray());
        return $user;
    }*/


    /**
     * The accessors to append to the model's array form.
    */
    //protected $appends = ['user'];

}