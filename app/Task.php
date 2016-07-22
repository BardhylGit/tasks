<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    
    protected $fillable = ['description', 'state', 'user_id'];
    
    public function user() 
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
