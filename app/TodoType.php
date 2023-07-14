<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoType extends Model
{
    protected $table = 'todo_types';
    protected $fillable = ['name'];
}
