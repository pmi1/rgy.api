<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discounter extends Model
{
    public static $returnedFields = [
        'discounter_id as id',
        'name',
        'percent_discounter as percentDiscounter',
        'description',
        'status',
    ];
    public $table = "discounter";
    public $primaryKey = 'discounter_id';
    protected $fillable = ['name', 'percent_discounter', 'description', 'status'];
}
