<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmfSequence extends Model
{
    public $table = 'cmf_sequence';

    public $primaryKey = 'cmf_sequence_id';

    static public function getString($key)
    {
        return (($t = self::where('sysname', '=', $key)->firstOrFail()) ? $t->value : null);
    }
}