<?php

namespace App\Http\Controllers\API;

use App\Exceptions\APIException;
use App\OrdersItem;
use App\Role;
use App\SearchFilters\ItemSearch\ItemSearch;
use App\SearchFilters\ItemSearch\ItemSearchResult;
use App\SearchFilters\OrderSearch\OrderSearch;
use App\SearchFilters\OrderSearch\OrderSearchResult;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\ItemContractor;
use App\Order;

class ItemController extends APIController
{
    public function index()
    {
        return response()->json(['success' => Item::all()], $this->successStatus);
    }

    public function show($id)
    {
        $item = Item::where('item.item_id', $id)
            ->leftJoin('item_contractor as ic',function($join) {
                 $join->on('item.item_id', '=', 'ic.item_id');
                 $join->on('item.cmf_site_id', '=', 'ic.cmf_site_id');
                 $join->on('ic.contractor_id', '=', DB::raw(3));
             })
            ->where('item.cmf_site_id', config('common.siteId', 1))
            ->get(
                [
                    'item.item_id',
                    'itemtype',
                    'typename',
                    'availablecount',
                    'availablecountcontractor',
                    'is_available',
                    'item.price',
                    'pricebuy',
                    'one_car',
                    'arendatype',
                    'repair',
                    'stelag',
                    'condition',
                    'item.bonus',
                    'bonusPay',
                    'discount_2_day',
                    'article',
                    'item.contractor_id',
                    'contractor2_id',
                    'contractor3_id',
                    'contractor4_id',
                    'ic.available_quantity',
                    //'ic.purchased_quantity'
                ]
            )
        ->first();
        if (empty($item)) {
            return response()->json(['success' => []], $this->successStatus);
        }


        $lastOrdersItemObject = $item->ordersItem()->orderBy('orders_id', 'desc')->get()->first();
        $contractors = [];
        if ($item['contractor_id']) array_push($contractors, $item->contractor_id);
        if ($item['contractor2_id']) array_push($contractors, $item->contractor2_id);
        if ($item['contractor3_id']) array_push($contractors, $item->contractor3_id);
        if ($item['contractor4_id']) array_push($contractors, $item->contractor4_id);

        $item = [
            'id' => $item['item_id'],
            'name' => $item['itemtype'],
            'type' => $item['typename'],
            'art' => $item['article'],
            'available' => $item['available_quantity'],
            'purchased_quantity' => $item['purchased_quantity'],
            'availableContractor' => $item['availablecountcontractor'],
            'enabled' => $item['is_available'],
            'price' => (int)$item['price'],
            'buyPrice' => (int)$item['pricebuy'],
            'oneCar' => $item['one_car'],
            'repair' => $item['repair'],
            'rentType' => $item['arendatype'],
            'stockRack' => $item['stelag'],
            'condition' => $item['condition'],
            'bonus' => $item['bonus'],
            'bonusPay' => $item['bonusPay'],
            'secondDayDiscount' => $item['discount_2_day'],
            'discount' => null,
            'overprice' => null,
            'lastOrder' => $lastOrdersItemObject?$lastOrdersItemObject->orders_id:null,
            'lastOrdered' => $lastOrdersItemObject?$lastOrdersItemObject->quantity:null,
            'contractors' => $contractors,
            'image' => $item->image
        ];

        return response()->json(['success' => $item], $this->successStatus);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasSupermanager()) {
            return response()->json(['success' => false], 200);
        }

        return response()->json(['success' => Item::create($request->all())], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WORKSHOP, Role::WAREHOUSE_CODES))) {
            return response()->json(['success' => false], 200);
        }

        $itemContractor = ItemContractor::firstOrNew([
            'item_id' => $id,
            'contractor_id' => 3,
            'cmf_site_id' => config('common.siteId', 1)
        ]);

        $fieldMaps = [
            'available' => 'available_quantity',
            'price' => 'price',
            'condition' => 'state',
            'bonus' => 'cashback',
            'bonusPay' => 'bonus',
            'buyPrice' => 'deposit',
        ];

        $fields = [];

        foreach ($request->all() as $key=>$val) {

            if (isset($fieldMaps[$key])) {

                $itemContractor[$fieldMaps[$key]] = $val;
            }
        }

        $itemContractor->id = ItemContractor::max('id')+1;
        $itemContractor->item_id = $id;
        $itemContractor->contractor_id = 3;
        $itemContractor->cmf_site_id = config('common.siteId', 1);
        $itemContractor->save();

        $fieldMaps = [
            'enabled' => 'is_available',
            'price' => 'price',
            'buyPrice' => 'pricebuy',
            'oneCar' => 'one_car',
            'available' => 'availablecount',
            'availableContractor' => 'availablecountcontractor',
            'stockRack' => 'stelag',
            'condition' => 'condition',
            'bonus' => 'bonus',
            'bonusPay' => 'bonusPay',
            'secondDayDiscount' => 'discount_2_day',
            'repair' => 'repair'
        ];

        $fields = [];
        foreach ($request->all() as $key=>$val) {
            if (isset($fieldMaps[$key])) {
                $fields[$fieldMaps[$key]] = $val;
            }
        }

        if (isset($fields['discount_2_day'])) {
            $fields['discount_3_day'] = $fields['discount_2_day'];
        }

        if ($contractos = $request->get('contractors')) {
            $contractors = explode(',', $contractos);
            if (isset($contractors[0])) {
                $fields['contractor_id'] = $contractors[0];
            }
            if (isset($contractors[1])) {
                $fields['contractor2_id'] = $contractors[1];
            }
            if (isset($contractors[2])) {
                $fields['contractor3_id'] = $contractors[2];
            }
            if (isset($contractors[3])) {
                $fields['contractor4_id'] = $contractors[3];
            }
        }

        $itemType = Item::find(['item_id' => $id, 'cmf_site_id' => config('common.siteId', 1)]);

        return response()->json(['success' => $itemType->update($fields)], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasSupermanager()) {
            return response()->json(['success' => false], 200);
        }

        $callType = Item::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WORKSHOP, Role::WAREHOUSE_CODES))) {
            return response()->json(['success' => false], 200);
        }

        $filters = $request->all();
        $filters['realstatus'] = 1;

        $itemQuery = ItemSearch::apply($filters);
        $orderType = 'item_id';
        $orderDirection = 'desc';
        if (isset($filters['orderType']) && !empty($filters['orderType'])) {
            $orderType = $filters['orderType'];
        }
        if (isset($filters['orderDirection']) && !empty($filters['orderDirection'])) {
            $orderDirection = $filters['orderDirection'];
        }
        $result = (new ItemSearchResult($itemQuery))
            ->addImage()
            ->order($orderType, $orderDirection)
            ->paginate($request->get('itemsPerPage', 100));
        $installDataFrom = $installDataTo = null;
        if (isset($filters['installDateFrom']) && !empty($filters['installDateFrom'])) {
            $installDataFrom = $filters['installDateFrom'];
        }
        if (isset($filters['installDateTo']) && !empty($filters['installDateTo'])) {
            $installDataTo = $filters['installDateTo'];
        }

        $orderFilters = $filters;
        $orderFilters['status'] = Order::busyOrderStatusStock;

        $orderFilters = array_filter($orderFilters, function ($key) {
            return in_array($key, ['status', 'installDateFrom', 'installDateTo', 'uninstallDateFrom', 'uninstallDateFrom']);
        }, ARRAY_FILTER_USE_KEY);

        $result->getCollection()->transform( function ($item)
        use ($filters, $installDataFrom, $installDataTo, $orderFilters) {
            list($totalCharge, $countOrders) = Item::getCountAndSumOrdersForItem(
                $item['id'],
                $installDataFrom,
                $installDataTo
            );

            $item['countOrders'] = $countOrders;
            $item['totalCharge'] = $totalCharge;

            $item['busy'] = (new Order())->getItemOrdersGroupByDate($item['id'], $orderFilters);
            $item['complects'] = (new Item())->prepare(DB::table('item_complect as ic')
                    ->select([
                            'ic.item_id as id',
                            'ic.quantity',
                            'i.typename',
                            'i.itemtype',
                            'i.article as art',
                            'i.availablecount as available',
                            'i.repair',
                            'i.stelag',
                            'ii.image'
                            ])
                    ->leftJoin('item as i',function($join) {
                         $join->on('i.item_id', '=', 'ic.item_id');
                         $join->on('i.cmf_site_id', DB::raw(config('common.siteId', 1)));
                     })
                    ->leftJoin('item_image as ii',function($join) {
                         $join->on('ii.item_id', '=', 'i.item_id');
                         $join->on('ii.cmf_site_id', '=', 'i.cmf_site_id');
                         $join->on('ii.ordering', DB::raw(1));
                     })
                    ->where('ic.status', '>', DB::raw(0))
                    ->where('ic.parent_id', $item['id'])
                    ->get());

            return $item;
        });
        return response()->json(['success' => $result], 200, [], JSON_NUMERIC_CHECK);
    }

    public function getOrders($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasSupermanager() || !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }
        $filters = $request->all();
        $orderBuilder = (new OrdersItem)->newQuery();
        $orderIDs = $orderBuilder->where('item_id', $id)->get(['orders_id']);
        $filters['IDs'] = $orderIDs->toArray();
        $orderQuery = OrderSearch::apply($filters);
        $orderType = 'installdate';
        $orderDirection = 'desc';
        if (isset($filters['orderType']) && !empty($filters['orderType'])) {
            $orderType = $filters['orderType'];
        }
        if (isset($filters['orderDirection']) && !empty($filters['orderDirection'])) {
            $orderDirection = $filters['orderDirection'];
        }
        $cloneOrderQuery = clone $orderQuery;
        $paginate = (new OrderSearchResult($orderQuery))
            ->addCustomer()
            ->addSumEachOrderByItems([$id])
            ->order($orderType, $orderDirection)
            ->addOrderStatus()
            ->paginate($request->get('itemsPerPage', 100));
        $equipmentTotalCharge = (new OrderSearchResult($cloneOrderQuery))->getEquipmentTotalCharge([$id]);
        $equipmentTotalAmount = (new OrderSearchResult($cloneOrderQuery))->getEquipmentTotalAmount([$id]);
        return response()->json(
            [
                'success' => [
                    'current_page' => $paginate->currentPage(),
                    'data' => $paginate->items(),
                    'last_page' => $paginate->lastPage(),
                    'prev_page_url' => $paginate->previousPageUrl(),
                    'total' => $paginate->total(),
                    'equipmentTotalCharge' => $equipmentTotalCharge,
                    'equipmentTotalAmount' => $equipmentTotalAmount
                ]
            ],
            200, [], JSON_NUMERIC_CHECK);
    }
}