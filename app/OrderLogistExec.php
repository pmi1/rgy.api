<?php

namespace App;

use App\OrderLogist;
use App\User;
use App\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLogistExec extends Model
{
    public $table = 'orders_logist_exec';

    protected $fillable = ['quantity', 'user_id', 'orders_logist_id', 'item_id', 'cdate', 'damaged', 'reason', 'parent_id'];

    public function orderLogist()
    {
        return $this->belongsTo(OrderLogist::class, 'orders_logist_id',  'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id',  'user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id',  'item_id');
    }

    public function parent()
    {
        return $this->belongsTo(Item::class, 'parent_id', 'item_id');
    }
}