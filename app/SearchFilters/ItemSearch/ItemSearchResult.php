<?php
/**
 * Created by PhpStorm.
 * CustomerID: 
 * Date: 2019-05-31
 * Time: 15:38
 */

namespace App\SearchFilters\ItemSearch;


use App\Item;
use App\SearchFilter\Result;
use function foo\func;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItemSearchResult extends Result
{
    public function __construct(Builder $builder)
    {
        parent::__construct($builder);
        $this->addDefaultFields();
    }

    public function distinct()
    {
        $this->builder->distinct();
    }
    private function addDefaultFields()
    {
        $this->builder->select([
            'item.item_id',
            'item.itemtype',
            'item.typename',
            'item.article',
            'item.availablecount',
            'item.availablecountcontractor',
            'item.is_available',
            'item.price',
            'item.pricebuy',
            'item.one_car',
            'item.repair',
            'item.arendatype',
            'item.stelag',
            'item.condition',
            'item.bonus',
            'item.bonusPay',
            'item.discount_2_day',
            'item.contractor_id',
            'item.contractor2_id',
            'item.contractor3_id',
            'item.contractor4_id',
        ])
        ->where('item.cmf_site_id', config('common.siteId', 1));
        //$this->builder->with('colors');
        return $this;
    }

    public function addImage()
    {
        //$this->builder->with('image');
        return $this;
    }

    public function paginate($countItemsPerPage = null)
    {
        $countItemsPerPage = $countItemsPerPage?$countItemsPerPage:env('PAGINATION_COUNT_PER_PAGE');
        $paginate = $this->builder->paginate($countItemsPerPage);
        $this->mapResult($paginate->getCollection());
        return $paginate;
    }

    public function get()
    {
        $limitItems = env('LIMIT_ITEMS', 1000);
        return $this->builder->limit($limitItems)->get();
    }

    public function addTotalAmount()
    {

    }

    public function order($orderType = 'item_id', $orderDirection = 'desc')
    {
        $this->builder->orderBy($orderType, $orderDirection);
        return $this;
    }

    private function mapResult($collection)
    {
        $collection->transform(function ($item) {
            $contractors = [];
            /*if ($item['contractor_id']) array_push($contractors, $item->contractor_id);
            if ($item['contractor2_id']) array_push($contractors, $item->contractor2_id);
            if ($item['contractor3_id']) array_push($contractors, $item->contractor3_id);
            if ($item['contractor4_id']) array_push($contractors, $item->contractor4_id);*/
            $lastOrdersItemObject = $item->ordersItem()->orderBy('orders_id', 'desc')->get()->first();
            /*$imagePath = null;
            if ($imageObject = $item->image()->get(['image'])->first()) {
                $imagePath = explode("#", $imageObject->image)[0];
            }*/

            foreach ($item->itemContractors as $value) {
                array_push($contractors, $value->contractor_id);

                if ($value->contractor_id == 3) {
                    $item->availablecount = $value->available_quantity;
                }
            }

            return  new Collection(
                [
                    'id' => $item->item_id,
                    'name' => $item->itemtype,
                    'type' => $item->typename,
                    'art' => $item->article,
                    'available' => $item->availablecount,
                    'availableContractor' => $item->availablecountcontractor,
                    'enabled' => $item->is_available,
                    'price' => $item->price,
                    'buyPrice' => $item->pricebuy,
                    'oneCar' => $item->one_car,
                    'repair' => $item->repair,
                    'rentType' => $item->arendatype,
                    'stockRack' => $item->stelag,
                    'condition' => $item->condition,
                    'bonus' => $item->bonus,
                    'bonusPay' => $item->bonusPay,
                    'secondDayDiscount' => $item->discount_2_day,
                    'discount' => null,
                    'overprice' => null,
                    'lastOrder' => $lastOrdersItemObject?$lastOrdersItemObject->orders_id:null,
                    'lastOrdered' => $lastOrdersItemObject?$lastOrdersItemObject->quantity:null,
                    'contractors' => $contractors,
                    'image' => $item->image
            ]);
        });
    }
}