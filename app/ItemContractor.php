<?php

namespace App;

use App\Item;
use App\Contractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemContractor extends Model
{
    public $table = 'item_contractor';

    protected $fillable = ['available_quantity', 'price', 'state', 'bonus', 'cashback', 'deposit'];

    public function product()
    {
        return $this->belongsTo(Item::class, 'item_id',  'item_id')->where('cmf_site_id', config('common.siteId', 1));
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id',  'contractor_id')->where('cmf_site_id', config('common.siteId', 1));
    }

}
