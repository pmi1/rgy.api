<?php

namespace App\SearchFilters\OrderSearch;


use App\SearchFilter\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderSearchResult extends Result
{
    private function setTableAlias()
    {
        $this->builder->from('orders as o');
    }

    public function __construct(Builder $builder)
    {
        parent::__construct($builder);
        $limitItems = env('LIMIT_ITEMS', 1000);
        $this->builder->limit($limitItems);
        $this->addDefaultFields();
    }

    private function addDefaultFields()
    {
        $this->builder->select(
            [
                'orders.orders_id as id',
                'orders.user_id',
                'orders.operator_id as managerId',
                'orders.orders_status_id as status',
                'orders.amount as amount',
                'orders.date_insert as ordered',
                'orders.installdate as installDate',
                'orders.enddate as uninstallDate',
                'orders.response_time as responseTime',
                'orders.order_cancel_reason_id as declineId',
                'orders.companyname as company',
                'orders.ocenka as rating',
            ]
        )
        ->where('orders.cmf_site_id', config('common.siteId', 1));
    }

    public function get(array $fields = ['*'])
    {
        return $this->builder->get($fields);
    }

    public function paginate($countItemsPerPage = null)
    {
        $countItemsPerPage = $countItemsPerPage?$countItemsPerPage:env('PAGINATION_COUNT_PER_PAGE');
        return $this->builder->paginate($countItemsPerPage);
    }

    public function addOrderStatus()
    {
        $this->builder->leftJoin('orders_status_comment as osc', function($join) {
            $join->on('osc.orders_id', '=', 'orders.orders_id');
            $join->on('osc.orders_status_id', '=', 'orders.orders_status_id');
        });
        $this->builder->addSelect('osc.comment as description');
        return $this;
    }

    public function order($orderType = 'installdate', $orderDirection = 'desc')
    {
        $this->builder->orderBy($orderType, $orderDirection);
        return $this;
    }

    public function addCustomer()
    {
        $this->builder->with(['customer' => function ($query) {
            $query->select(
                'user.user_id',
                'name',
                'lastname as lastName',
                'secondname as secondName',
                'user.email',
                'user.phone',
                'user.discount',
                DB::raw('count(DISTINCT orders.orders_id) as ordered'),
                DB::raw('sum(if(orders.orders_status_id = 3 or orders.orders_status_id = 9, 1, 0)) as done')
            );
            $query->leftJoin('orders', function ($join) {
               $join->on('orders.user_id', '=', 'user.user_id');
            })
            ->groupBy(['orders.user_id']);

        }]);
        return $this;
    }

    public function addSumEachOrderByItems(array $itemIDs)
    {
        $this->builder->leftJoin('orders_item', function ($join) {
           $join->on('orders_item.orders_id', '=', 'orders.orders_id');
        });
        $this->builder->addSelect([
                'orders_item.quantity',
                DB::raw('sum(orders_item.quantity) as quantity'),
                'price as priceEquipment',
                DB::raw('sum(orders_item.quantity) * orders_item.price as chargeEquipment'),
            ])
            ->whereIn('orders_item.item_id', $itemIDs)
            ->groupBy(['orders.orders_id']);
        return $this;
    }

    public function getEquipmentTotalCharge(array $itemIDs)
    {
        $this->builder->leftJoin('orders_item as item_total_charge', function ($join) {
            $join->on('item_total_charge.orders_id', '=', 'orders.orders_id');
        });
        $this->builder->select(DB::raw('sum(item_total_charge.quantity * item_total_charge.price) as equipmentTotalCharge'));
        $this->builder->whereIn('item_total_charge.item_id', $itemIDs);
        $this->builder->groupBy(['item_total_charge.item_id']);
        if ($result = $this->builder->first(['equipmentTotalCharge'])) {
            return $result->toArray()['equipmentTotalCharge'];
        }
        return null;
    }

    public function getEquipmentTotalAmount(array $itemIDs)
    {
        $this->builder->leftJoin('orders_item as item_total_amount', function ($join) {
            $join->on('item_total_amount.orders_id', '=', 'orders.orders_id');
        });
        $this->builder->select(DB::raw('sum(item_total_amount.quantity) as equipmentTotalAmount'));
        $this->builder->whereIn('item_total_amount.item_id', $itemIDs);
        $this->builder->groupBy(['item_total_amount.item_id']);
        if ($result = $this->builder->first(['equipmentTotalAmount'])) {
            return $result->toArray()['equipmentTotalAmount'];
        }
        return null;
    }
}