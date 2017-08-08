<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
 	protected $table = 'roles';
  public $timestamps = true;
	
   /**
   * The users that belong to the role.
   */
  public function users()
  {
    //return $this->belongsToMany('App\User', 'role_user','user_id', 'role_id');
    return $this->belongsToMany('App\User');  
  }

  /**
  * The Permissions that belong to the role.
  */
  public function permissions()
  {
    return $this->belongsToMany('App\Permission');
  }
}