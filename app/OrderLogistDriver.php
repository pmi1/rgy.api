<?php

namespace App;

use App\OrderLogist;
use App\User;
use App\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLogistDriver extends Model
{
    public $table = 'orders_logist_driver';

    protected $fillable = ['main', 'driver', 'orders_logist_id', 'car', 'trip_count'];

    public function orderLogist()
    {
        return $this->belongsTo(OrderLogist::class, 'orders_logist_id',  'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'driver',  'user_id');
    }

    public function carLink()
    {
        return $this->belongsTo(Car::class, 'car',  'car_id');
    }

}