<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallVotesLinkCallRating extends Model
{
    protected $fillable = ['call_vote_id', 'value', 'rating_id', 'manager_id'];
}
