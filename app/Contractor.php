<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    public $table = 'contractor';

    protected $fillable = ['name', 'max_discount_percent_for_contractor', 'discount_percent_for_rguys', 'description', 'image', 'address',
        'address_sklad', 'site', 'city', 'phone', 'emails', 'user_name', 'user_lastname', 'show_logo', 'force_delivery', 'status', 'city_id'];

    public $primaryKey = 'contractor_id';

    public $maps = [
        'id' => 'contractor_id'
    ];

    public function itemContractor()
    {
        return $this->hasMany(ItemContractor::class, 'contractor_id', 'contractor_id');
    }

    public function cmfSite()
    {
        return $this->belongsTo(CmfSite::class, 'cmf_site_id', 'cmf_site_id');
    }

    /**
     * alias itemContractor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->itemContractor();
    }

    /**
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function setFilter($filters) {
        $queryBuilder = (new self())->newQuery();
        if (isset($filters['itemIDs']) && !empty($filters['itemIDs'])) {
            $itemIDs = $filters['itemIDs'];

            $queryBuilder->whereHas('itemContractor', function ($query) use ($itemIDs) {
                   $query->whereIn('item_id', $itemIDs);
                });
        }

        return $queryBuilder;
    }
}
