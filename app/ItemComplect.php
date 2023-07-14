<?php

namespace App;

use App\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemComplect extends Model
{
    public $table = 'item_complect';

    public $primaryKey = 'item_complect_id';

    public function product()
    {
        return $this->belongsTo(Item::class, 'item_id', 'parent_id');
    }

    public function accessory()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}
