<?php

namespace App;

use App\OrderLogist;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLogistHelper extends Model
{
    public $table = 'orders_logist_helper';

    protected $fillable = ['main', 'name', 'orders_logist_id'];

    public function orderLogist()
    {
        return $this->belongsTo(OrderLogist::class, 'orders_logist_id',  'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'name',  'user_id');
    }

}