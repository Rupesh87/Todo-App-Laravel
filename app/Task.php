<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [ 
        'user_id', 'admin_id', 'description', 'task_title', 'status' , 'priority', 'duedate'
    ] ;

    public function user() {
        return $this->belongsTo('App\User') ;
    }

}
