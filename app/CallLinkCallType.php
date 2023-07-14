<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLinkCallType extends Model
{
    protected $fillable = ['call_entry_id', 'call_type_id'];

    public function callType()
    {
        return $this->belongsTo(CallType::class);
    }
}
