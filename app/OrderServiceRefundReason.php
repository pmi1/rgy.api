<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderServiceRefundReason extends Model
{

    public $table = 'orders_service_refund_reason';

    public $primaryKey = 'id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'service_refund_reason_id');
    }

}