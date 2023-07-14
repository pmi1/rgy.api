<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPaymentStatusLog extends Model
{
    public $table = 'orders_payment_status_log';

    protected $fillable = ['user_id', 'cdate', 'orders_id', 'status_id'];
}