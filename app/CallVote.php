<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallVote extends Model
{
    protected $fillable = ['comment', 'entry_id'];
}
