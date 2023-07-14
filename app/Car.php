<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Car extends Model
{
    public $table = 'car';
    public $primaryKey = 'car_id';
}