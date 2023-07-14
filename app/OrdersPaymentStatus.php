<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersPaymentStatus extends Model
{
    protected $table = 'orders_payment_status';

    protected $fillable = ['name', 'ordering'];
}
