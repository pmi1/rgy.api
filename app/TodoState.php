<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoState extends Model
{
    protected $table = 'todo_states';
    protected $fillable = ['name', 'color'];
}
