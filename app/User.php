<?php namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'first_name', 'middle_name', 'last_name', 'sur_name', 'telefono', 'celular'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'persist_code',
    ];

    
    /**
     * genera el nombre completo del usuario
     *
     * @return bool
     */
    public function getfullnameAttribute()
    {
        return $this->attributes['first_name'] .' '. $this->attributes['last_name'];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['fullname'];

    
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    // un usuario puede ser administrador de uno o muchos bloques
    public function blqadmins() {
        return $this->hasMany('App\Blqadmin');
    }
    
    // un usuario puede hacer una o muchas bitacoras
    public function bitacoras() {
        return $this->hasMany('App\Bitacora');
    }    

    public function prop()
    {
        return $this->hasOne('App\Prop');
    }
}
