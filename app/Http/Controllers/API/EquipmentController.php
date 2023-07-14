<?php

namespace App\Http\Controllers\API;

use App\User;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\APIException;
use Carbon\Carbon;
use App\Order;

class EquipmentController extends APIController
{
    public function items(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        if (!$user->hasSupermanager() && !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $query = DB::table('orders_item')
            ->select([
                DB::raw('orders_item.item_id id'),
                DB::raw('orders_item.item_id art'),
                DB::raw('item.itemtype name'),
                DB::raw('item.typename as type'),
                DB::raw('count(orders_item.item_id) as orders'),
                DB::raw('item.view_count as viewed'),
                DB::raw('item.availablecount as available'),
                DB::raw('item.price as price'),
                DB::raw('
                    SUM(CASE orders.orders_status_id
                        WHEN 4 
                            THEN 
                                CASE 
                                   WHEN orders_item_begin.orders_item_id IS NULL 
                                   THEN (orders_item.quantity * orders_item.price)
                                   ELSE (orders_item_begin.quantity * orders_item_begin.price)
                                END
                             ELSE
                                (orders_item.quantity * orders_item.price)
                    END) as charge
                '),
                DB::raw('
                    SUM(CASE orders.orders_status_id
                        WHEN 4 
                            THEN 
                                CASE
                                   WHEN orders_item_begin.orders_item_id IS NULL
                                   THEN orders_item.quantity
                                   ELSE orders_item_begin.quantity
                                END
                             ELSE
                                orders_item.quantity
                    END)  as ordered
                '),
            ])
            ->leftJoin('item',function($join) {
                 $join->on('item.item_id', '=', 'orders_item.item_id');
                 $join->on('item.cmf_site_id', DB::raw(1));
             })
            ->leftJoin('orders', 'orders.orders_id', '=', 'orders_item.orders_id')
            ->leftJoin('orders_item_begin', 'orders_item_begin.orders_item_id', '=', 'orders_item.orders_item_id')
            ->whereNotIn('orders.orders_status_id', [4, 36])
            ->groupBy('orders_item.item_id');

        $orderFilters = [];
        if ($request->has('installDateFrom')) {
            $orderFilters['installDateFrom'] = $request->get('installDateFrom');
            $query->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$orderFilters['installDateFrom']]);
        }

        if ($request->has('installDateTo')) {
            $orderFilters['installDateTo'] = $request->get('installDateTo');
            $query->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$orderFilters['installDateTo']]);
        }

        if ($orderCancelStatus = $request->get('orderCancelStatus')) {
            $query->whereIn('orders.order_cancel_reason_id', json_decode($orderCancelStatus));
        }
        if ($orderStatus = $request->get('orderStatus')) {
            $query->whereIn('orders.orders_status_id', json_decode($orderStatus));
        }

        if($q = $request->get('query')) {
            $query->whereRaw("
                (item.itemtype like '%$q%' or item.typename like '%$q%' or item.article like '%$q%')
            ");
        }

        $pagination = $query->paginate(env('PAGINATION_COUNT_PER_PAGE'));

        foreach ($pagination->items() as &$resultItem) {

            $images = DB::table('item_image')
                ->leftJoin('image_format_image',function($join) {
                     $join->on('image_format_image.row_id', '=', 'item_image.item_image_id');
                     $join->on('image_format_image.cmf_site_id', DB::raw(1));
                 })
                ->leftJoin('image_format', 'image_format.image_format_id', '=', 'image_format_image.image_format_id')
                ->whereIn('image_format_image.image_format_id', [33, 2])
                ->where('item_id', $resultItem->id)
                ->where('item_image.cmf_site_id', DB::raw(1))
                ->limit(1)
                ->get(['image_format_image.image', 'image_format.sysname', 'image_format.width', 'image_format.height']);

            $resultItem->images = array_map(
                function ($item) {
                    return [
                        'sysname' => $item->sysname,
                        'src' => explode('#', $item->image)[0],
                        'width' => $item->width,
                        'height' => $item->height,

                    ];
                },
                $images->toArray()
            );
        }

/*
        $ordersSubJoin = DB::table('orders_item')
            ->select([
                DB::raw('max(orders_item.item_id ) as item_id'),
                DB::raw('sum(orders_item.quantity_back) as back'),
                DB::raw('sum(orders_item.quantity_repair) as repair'),
                DB::raw('sum(orders_item.quantity_lost) as lost'),
                DB::raw('count(orders_item.item_id) as orders'),
                DB::raw('max(orders.orders_status_id) as status_id'),
                DB::raw('
                    SUM(CASE orders.orders_status_id
                        WHEN 4 
                            THEN 
                                CASE 
                                   WHEN orders_item_begin.orders_item_id IS NULL 
                                   THEN (orders_item.quantity * orders_item.price)
                                   ELSE (orders_item_begin.quantity * orders_item_begin.price)
                                END
                             ELSE
                                (orders_item.quantity * orders_item.price)
                    END) as charge        
                '),
                DB::raw('
                    SUM(CASE orders.orders_status_id
                        WHEN 4 
                            THEN 
                                CASE
                                   WHEN orders_item_begin.orders_item_id IS NULL
                                   THEN orders_item.quantity
                                   ELSE orders_item_begin.quantity
                                END
                             ELSE
                                orders_item.quantity
                    END)  as ordered        
                '),
            ])
            ->leftJoin('orders', 'orders.orders_id', '=', 'orders_item.orders_id')
            ->leftJoin('orders_item_begin', 'orders_item_begin.orders_item_id', '=', 'orders_item.orders_item_id')
            ->groupBy('orders_item.item_id');

        $orderFilters = [];
        if ($request->has('installDateFrom')) {
            $orderFilters['installDateFrom'] = $request->get('installDateFrom');
            $ordersSubJoin->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$orderFilters['installDateFrom']]);
        }

        if ($request->has('installDateTo')) {
            $orderFilters['installDateTo'] = $request->get('installDateTo');
            $ordersSubJoin->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$orderFilters['installDateTo']]);
        }

        if ($orderCancelStatus = $request->get('orderCancelStatus')) {
            $ordersSubJoin->whereIn('orders.order_cancel_reason_id', json_decode($orderCancelStatus));
        }
        if ($orderStatus = $request->get('orderStatus')) {
            $ordersSubJoin->whereIn('orders.orders_status_id', json_decode($orderStatus));
        }

        $requestItem = DB::table('item')
            ->select([
                DB::raw('item.item_id as id'),
                DB::raw('item.itemtype as name'),
                DB::raw('item.typename as type'),
                DB::raw('item.article as art'),
                DB::raw('item.availablecount as available'),
                DB::raw('item.price as price'),
                DB::raw('item.view_count as viewed'),
                DB::raw('item.pledge as pledge'),
                DB::raw('item.condition as `condition`'),
                DB::raw('item.one_car as oneCar'),
                DB::raw('item.stock_id as stockId'),
                DB::raw('item.bonus as bonus'),
                DB::raw('item.bonusPay as bonusPay'),
                DB::raw('item.contractor_id as contractor_id'),
                DB::raw('item.contractor2_id as contractor2_id'),
                DB::raw('item.contractor3_id as contractor3_id'),
                DB::raw('item.contractor4_id as contractor4_id'),
                'ord.orders',
                'ord.back',
                'ord.repair',
                'ord.lost',
                'ord.charge',
                'ord.ordered',

            ])
            ->leftJoinSub(
                DB::table('cat_item')
                    ->select('item_id')
                ->whereIn('catalogue_id', explode(',', env('DEFAULT_CAT_ITEMS')))
                ->groupBy('item_id'), 'catItem', 'catItem.item_id', '=', 'item.item_id'
            )
            ->whereNotNull('catItem.item_id')
            ->leftJoinSub(
                $ordersSubJoin, 'ord', 'item.item_id', '=', 'ord.item_id'
            )
            ->WhereNotNull('ord.item_id');

        $requestItem->where('status', 1);
        $sortType = $request->has('sortType')?$request->get('sortType'):'charge';
        $sortDir = $request->has('sortDir')?$request->get('sortDir'):'desc';
        $requestItem->orderBy($sortType, $sortDir);

        if($query = $request->get('query')) {
            $requestItem->whereRaw("
                (item.itemtype like '%$query%' or item.typename like '%$query%' or item.article like '%$query%')
            ");

        }

            $pagination = $requestItem->paginate(env('PAGINATION_COUNT_PER_PAGE'));
        foreach ($pagination->items() as &$resultItem) {
            $resultItem->id = (int)$resultItem->id;
            $resultItem->price = (int)$resultItem->price;
            $resultItem->viewed = (int)$resultItem->viewed;
            $resultItem->pledge = (int)$resultItem->pledge;
            $resultItem->condition = (int)$resultItem->condition;
            $resultItem->oneCar = (int)$resultItem->oneCar;
            $resultItem->stockId = (int)$resultItem->stockId;
            $resultItem->bonus = (int)$resultItem->bonus;
            $resultItem->bonusPay = (int)$resultItem->bonusPay;
            $resultItem->contractor_id = (int)$resultItem->contractor_id;
            $resultItem->contractor2_id = (int)$resultItem->contractor2_id;
            $resultItem->contractor3_id = (int)$resultItem->contractor3_id;
            $resultItem->ordered = (int)$resultItem->ordered;
            $resultItem->charge = (int)$resultItem->charge;
            $resultItem->back = (int)$resultItem->back;
            $resultItem->repair = (int)$resultItem->repair;
            $resultItem->lost = (int)$resultItem->lost;


            $images = DB::table('item_image')
                ->leftJoin('image_format_image', 'image_format_image.row_id', '=', 'item_image.item_image_id')
                ->leftJoin('image_format', 'image_format.image_format_id', '=', 'image_format_image.image_format_id')
                ->whereIn('image_format_image.image_format_id', [33, 2])
                ->where('item_id', $resultItem->id)
                ->get(['image_format_image.image', 'image_format.sysname', 'image_format.width', 'image_format.height']);

            $resultItem->images = array_map(
                function ($item) {
                    return [
                        'sysname' => $item->sysname,
                        'src' => explode('#', $item->image)[0],
                        'width' => $item->width,
                        'height' => $item->height,

                    ];
                },
                $images->toArray()
            );

            $categories = DB::table('catalogue')
                ->leftJoin('cat_item_link', 'cat_item_link.catalogue_id', '=', 'catalogue.catalogue_id')
                ->where('cat_item_link.item_id', $resultItem->id)
                ->get(
                    [
                        'catalogue.catalogue_id',
                        'catalogue.name',
                    ]
                );
            $resultItem->categories = $categories;


            $requestOrderTable = DB::table('orders')
                ->select(
                    [
                        DB::raw('max(orders_item.item_id ) as item_id'),
                        DB::raw('count( DISTINCT orders.orders_id ) as orders'),
                        DB::raw('max(orders.begindate ) as begindate'),
                        DB::raw('max(orders.begintime ) as begintime'),
                        DB::raw('max(orders.enddate ) as enddate'),
                        DB::raw('max(orders.endtime ) as endtime'),
                        DB::raw('max(orders.installdate ) as installdate'),
                        DB::raw('max(orders.installtime ) as installtime'),
                        DB::raw('max(orders.endmanagerdate ) as endmanagerdate'),
                        DB::raw('max(orders.endmanagertime ) as endmanagertime'),
                        DB::raw('max(orders.date_start_execution ) as date_start_execution'),
                        DB::raw('max(orders.date_end_execution ) as date_end_execution'),
                        DB::raw('if(orders_cancel_reasons.id is null, 0, orders_cancel_reasons.id) as cancel_id'),
                        DB::raw('sum(orders_item.price * orders_item.quantity) as charge'),
                        DB::raw('sum(orders_item.quantity_back) as back'),
                        DB::raw('sum(orders_item.quantity_repair) as repair'),
                        DB::raw('max(orders_item.orders_item_id) as orders_item_id'),
                        DB::raw('sum(orders_item.quantity_lost) as lost'),
                        DB::raw('sum(orders_item.quantity) as quantity2'),
                        DB::raw('max(orders_item_begin.orders_id) as test'),
                                DB::raw('
                                    sum(CASE orders.orders_status_id
                                        WHEN 4 
                                            THEN 
                                                CASE
                                                   WHEN orders_item_begin.orders_id IS NULL
                                                   THEN orders_item.price * orders_item.quantity
                                                   ELSE orders_item_begin.price * orders_item_begin.quantity
                                                END
                                             ELSE
                                                orders_item.price * orders_item.quantity
                                    END) as charge       
                            '),
                                DB::raw('
                                    sum(CASE orders.orders_status_id
                                        WHEN 4 
                                            THEN 
                                                CASE 
                                                   WHEN orders_item_begin.orders_id IS NULL 
                                                   THEN orders_item.quantity
                                                   ELSE orders_item_begin.quantity
                                                END
                                             ELSE
                                                orders_item.quantity
                                        END
                                    )  as ordered        
                            '),
                        ]
                )
                ->leftJoin('orders_item', 'orders.orders_id', '=', 'orders_item.orders_id')
                ->leftJoin('orders_item_begin', 'orders_item_begin.orders_item_id', '=', 'orders_item.orders_item_id')
                ->leftJoin('orders_cancel_reasons', 'orders_cancel_reasons.id', '=', 'orders.order_cancel_reason_id')
                ->groupBy(['cancel_id']);
            $requestOrderTable->where('orders_item.item_id', $resultItem->id);
            if ($request->has('installDateFrom')) {
                $installDateFrom = $request->get('installDateFrom');
                $requestOrderTable->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$installDateFrom]);
            }

            $installDateTo = $request->get('installDateTo', false);

            if ($installDateTo) {
                $requestOrderTable->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$installDateTo]);
            }

            if ($orderCancelStatus = $request->get('orderCancelStatus')) {
                $requestOrderTable->whereIn('orders.order_cancel_reason_id', json_decode($orderCancelStatus));
            }
            if ($orderStatus = $request->get('orderStatus')) {
                $requestOrderTable->whereIn('orders.orders_status_id', json_decode($orderStatus));
            }
            $resultItem->ordersStat = [];
            foreach ($requestOrderTable->get() as $order) {
                $resultItem->ordersStat[] = [
                    "ordered" => (int)$order->ordered,
                    "orders" => (int)$order->orders,
                    "cancel_id" => (int)$order->cancel_id,
                    "charge" => (int)$order->charge,
                    "back" => (int)$order->back,
                    "repair" => (int)$order->repair,
                    "lost" => (int)$order->lost,
                ];
            }

            $resultItem->busy = (new Order())->getItemOrdersGroupByDate($resultItem->id, $orderFilters);
        }
*/
        return response()->json(['success' => $pagination, 'request' => $request->toArray()], $this->successStatus, [], JSON_NUMERIC_CHECK);

    }
}