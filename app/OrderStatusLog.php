<?php

namespace App;

use App\Order;
use App\User;
use App\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStatusLog extends Model
{
    public $table = 'orders_status_log';

    protected $fillable = ['user_id', 'cdate', 'orders_id', 'status_id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id', 'orders_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'orders_status_id');
    }
}