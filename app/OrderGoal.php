<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderGoal extends Model
{

    public $table = 'orders_goal';

    public $primaryKey = 'id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'orders_goal_id');
    }

}