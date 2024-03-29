<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
  public function getFullNameAttribute()
  {
    return $this->attributes['first_name'] .' '. $this->attributes['last_name'];
  }
  // echo User::find(1)->full_name;
  
  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = ['full_name'];


  /**
   * The roles that belong to the user.
   */

  public function roles()
  {
    //return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
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

  public function props()
  {
    return $this->hasMany('App\Prop');
  }
}
