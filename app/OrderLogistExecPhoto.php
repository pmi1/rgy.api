<?php

namespace App;

use App\OrderLogist;
use App\User;
use App\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLogistExecPhoto extends Model
{
    public $table = 'orders_logist_exec_photo';

    protected $fillable = ['image', 'orders_logist_id', 'item_id',];

    public function orderLogist()
    {
        return $this->belongsTo(OrderLogist::class, 'orders_logist_id',  'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id',  'item_id');
    }

}