<?php

namespace App;

use App\Order;
use App\Item;
use App\Contractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderComplect extends Model
{

    public $table = 'orders_complect';

    public $primaryKey = 'id';

    protected $fillable = ['orders_id', 'item_id', 'contractor_id', 'quantity', 'condition', 'is_collected',
        'quantity_lost', 'quantity_repair', 'quantity_back'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id', 'orders_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id')->where('cmf_site_id', config('common.siteId', 1));
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id', 'contractor_id')->where('cmf_site_id', config('common.siteId', 1));
    }

}