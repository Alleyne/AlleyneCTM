<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
 	protected $table = 'permissions';
    public $timestamps = false;
 	
     /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}