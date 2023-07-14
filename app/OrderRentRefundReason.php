<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRentRefundReason extends Model
{

    public $table = 'orders_rent_refund_reason';

    public $primaryKey = 'id';

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'rent_refund_reason_id');
    }

}