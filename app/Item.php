<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Catalogue;
use App\Tax;
use App\ItemComplect;
use App\ItemUserCategory;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use App\ItemContractor;
use App\Traits\CompositePrimaryKey;

class Item extends Model
{
    public $table = 'item';

    protected $fillable = ['name', 'typename', 'article', 'availablecount', 'availablecountcontractor', 'enabled',
        'is_available', 'status', 'price', 'pricebuy', 'one_car', 'bonus', 'bonusPay', 'condition', 'contractor_id',
        'contractor2_id', 'contractor3_id', 'contractor4_id', 'stelag', 'discount_2_day', 'discount_3_day', 'repair'];

    public $primaryKey = ['item_id', 'cmf_site_id'];
    use CompositePrimaryKey;

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'landlord', 'id');
    }

    public function catalogue()
    {
        return $this->belongsToMany(Catalogue::class, 'cat_item_link', 'catalogue_id', 'item_id');
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'item_id');
    }

    public function userCategories()
    {
        return $this->hasMany(ItemUserCategory::class, 'item_id', 'item_id');
    }

    public function getImageAttribute()
    {
        $result = null;

        if ($this->item_id) {

           $t = DB::table('item_image as ii')
                    ->select(['image'])
                    -> where('ii.cmf_site_id', config('common.siteId', 1))
                    -> where('ii.item_id', DB::raw($this->item_id))
                    -> orderBy('ii.ordering')
                    -> first();

            if ($t && $t->image) {

                $f = explode('#', $t->image);
                $result = $f[0];
            }
        }

        return $result;
    }

    public function ordersItem()
    {
        return $this->hasMany(OrdersItem::class, 'item_id', 'item_id');
    }

    public function accessories()
    {
        return $this->hasMany(ItemComplect::class, 'parent_id', 'item_id');
    }

    public function getList($options)
    {
        $fields = [
            't.item_id as id',
            't.itemtype as name',
            't.typename as type',
            't.article as art',
            't.availablecount as available',
            't.price',
            'ii.image',
            isset($options['pid']) && $options['pid'] ? DB::raw('ifnull(ip.status,1) as status') : 't.status'
        ];

        if (isset($options['fields']) && $options['fields']) {
            $fields = array_merge($fields, $options['fields']);
        }

        $sql = DB::table('item as t')
            ->select($fields)
            ->leftJoin('item_image as ii', function ($join) {
                $join->on('ii.item_id', '=', 't.item_id');
                $join->on('ii.cmf_site_id', '=', 't.cmf_site_id');
                $join->on('ii.ordering', DB::raw(1));
            })
            ->where('t.cmf_site_id', config('common.siteId', 1));

        if (isset($options['pid']) && $options['pid']) {
            $sql->leftJoin('item_partner as ip', function ($join) use ($options) {
                $join->on('ip.item_id', '=', 't.item_id');
                $join->on('ip.stand_id', DB::raw($options['pid']));
            });
        }

        if (isset($options['catalogue']) && $options['catalogue']) {
            $sql->leftJoin('cat_item as ci', function ($join) use ($options) {
                $join->on('ci.item_id', '=', 't.item_id');
                $join->on('ci.cmf_site_id', '=', 't.cmf_site_id');
            })
                ->where('ci.catalogue_id', DB::raw($options['catalogue']));
        }

        if (isset($options['order']) && $options['order']) {
            $sql->leftJoin('orders_item as oi', function ($join) use ($options) {
                $join->on('oi.item_id', '=', 't.item_id');
            })
                ->where('oi.orders_id', DB::raw($options['order']));
        }

        if (isset($options['similarFor']) && $options['similarFor']) {
            $sql->join('item_link as il', function ($join) use ($options) {
                $join->on('il.linked_item_id', '=', 't.item_id');
                $join->on('il.cmf_site_id', '=', 't.cmf_site_id');
                $join->on('il.item_link_type_id', DB::raw(4));
                $join->on('il.item_id', DB::raw($options['similarFor']));
            });
        }

        return $sql;
    }

    public function materials()
    {
        return $this->belongsToMany(EquipmentMaterial::class, 'item_equipment_materials', 'item_id');
    }

    public function conditions()
    {
        return $this->hasOne(EquipmentCondition::class, 'id', 'condition');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_item', 'item_item_id');
    }

    public function prepare($is)
    {
        Image::configure(array('driver' => 'imagick'));
        foreach ($is as $key => &$value) {

            if (! isset($value->image) && isset($value->id)) {

                $value->image = (($t = DB::table('item_image as ii')
                    ->select(['image'])
                    -> where('ii.cmf_site_id', config('common.siteId', 1))
                    -> where('ii.item_id', DB::raw($value->id))
                    -> orderBy('ii.ordering')
                    -> first()) ? $t->image : null);
            }

            $f = explode('#', $value->image);
            $value->image = $f[0];
        }
        unset($value);

        return $is;
    }


    public static function getCountAndSumOrdersForItem(int $itemID, $installDateFrom, $installDateTo)
    {
        $queryOrdersItem = (new OrdersItem)->newQuery()
            ->leftJoin('orders', function ($join) {
                $join->on('orders.orders_id', '=', 'orders_item.orders_id');
            });
        if (!empty($installDateFrom)) {
            $queryOrdersItem->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$installDateFrom]);
        }
        if (!empty($installDateTo)) {
            $queryOrdersItem->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$installDateTo]);
        }
        $queryOrdersItem->where('item_id', $itemID);
        $sum = $queryOrdersItem->sum('price');
        $count = $queryOrdersItem->distinct('orders.orders_id')->count('orders.orders_id');
        return [$sum, $count];
    }

    public function itemContractors()
    {
        return $this->hasMany(ItemContractor::class, 'item_id', 'item_id')->where('cmf_site_id', config('common.siteId', 1));
    }
}

