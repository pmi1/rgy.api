<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    public static $fieldsResponse = [
        'status_id as id',
        'name',
        'status_discount as statusDiscount',
        'status_percent_bonuses as statusPercentBonuses',
        'oborot_from as oborotFrom',
        'oborot_to as oborotTo',
    ];

    public $table = "user_status";
    public $primaryKey = 'status_id';
    protected $fillable = ['name', 'status_discount', 'status_percent_bonuses', 'oborot_from', 'oborot_to'];
}
