<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStatus extends Model
{

    public $table = 'orders_status';

    public $primaryKey = 'orders_status_id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'orders_status_id', 'orders_status_id');
    }

}