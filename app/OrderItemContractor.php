<?php

namespace App;

use App\Item;
use App\Contractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItemContractor extends Model
{
    public $primaryKey = 'orders_item_contractor_id';

    public $table = 'orders_item_contractor';

    protected $fillable = ['orders_item_id', 'contractor_id', 'quantity', 'deposit', 'price', 'item_id'];

    public function forItem()
    {
        return $this->hasOneThrough(Item::class, OrdersItem::class, 'orders_item_id', 'item_id', 'orders_item_id', 'item_id');
    }

    public function getForIdAttribute()
    {
        return $this->orderItem ? $this->orderItem->item_id : null;
    }

    public function orderItem()
    {
        return $this->belongsTo(OrdersItem::class, 'orders_item_id', 'orders_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id',  'contractor_id')->where('cmf_site_id', config('common.siteId', 1))
;
    }

    public function getCheckedAttribute()
    {
        return $this->status == 1;
    }

    public function getVisibleAttribute()
    {
        return $this->status != 2;
    }
}