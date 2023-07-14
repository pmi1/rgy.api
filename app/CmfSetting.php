<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmfSetting extends Model
{
    public $table = 'cmf_settings';

    public $primaryKey = 'cmf_setting_id';

    static public function getString($key)
    {
        return (($t = self::where('sysname', '=', $key)->firstOrFail()) ? $t->value : null);
    }
}