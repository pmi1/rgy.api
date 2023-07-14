<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDocument extends Model
{

    public $table = 'orders_document';

    public $primaryKey = 'id';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'orders_id');
    }

    public function legalEntity()
    {
        return $this->belongsTo(LegalEntity::class, 'landlord', 'id');
    }

}