<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderEventType extends Model
{

    public $table = 'orders_event_type';

    public $primaryKey = 'id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'orders_event_type_id');
    }

}