<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Order;
use App\CashbackOperation;
use App\CashbackType;

class UserCashback extends Model
{
    public $table = 'user_cashback';
    public $primaryKey = 'id';

    const accrual = 1;
    const payment = 2;
    const paymentBlocked = 3;

    protected $fillable = ['user_id', 'price', 'cdate', 'type', 'order_id', 'by_user_id', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'orders_id');
    }

    public function operation()
    {
        return $this->belongsTo(CashbackOperation::class, 'type', 'id');
    }

    public function paymentType()
    {
        return $this->belongsTo(CashbackType::class, 'CashbackType', 'id');
    }
}
