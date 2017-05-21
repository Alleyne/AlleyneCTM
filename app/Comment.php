<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    public $timestamps = true;     
    
    public function post()
    {
    	return $this->belongsTo('App\Post');
    }
}
