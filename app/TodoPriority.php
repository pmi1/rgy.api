<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoPriority extends Model
{
    protected $table = 'todo_priorities';
    protected $fillable = ['name', 'color'];
}
