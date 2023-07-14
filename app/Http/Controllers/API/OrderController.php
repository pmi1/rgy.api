<?php

namespace App\Http\Controllers\API;

use App\Exceptions\APIException;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use App\UserCashback;
use App\Item;
use App\Order;
use App\OrdersItem;
use App\OrderItemContractor;
use App\OrderLogist;
use App\OrderStatusComment;
use App\OrderStatusLog;
use App\OrderPaymentStatusLog;
use App\TodoManager;
use App\CmfSetting;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Helper;
use View;

class OrderController extends APIController
{
    public function list()
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        $orders = [];
        if (!$user->hasContractor() || !$user->hasSupermanager()) {
            $ordersID = DB::table('orders_contractor as oc')
                //->leftJoin('orders_status', 'orders_status.orders_status_id', '=','oc.orders_status_id')
                ->where('oc.contractor_id', $user->contractor_id)
                ->where('oc.status', 1)
                ->get()
                ->map(
                    function ($item) {
                        return $item->orders_id;
                    }
                );

            $orders = DB::table('orders as o')
                ->select(
                    [
                        'o.orders_id as orders_id',
                        'o.orders_status_id as orders_status_id',
                        'o.date_insert as date_insert',
                        'o.description as description',
                        'o.address as address',
                        'o.stage_id as stage',
                        'o.prepayment as paymentSum',
                        'o.installdate as installdate',
                        'o.installdate as installdate',
                        'o.installtime as installtime',
                        'o.date_end_execution as date_end_execution',
                        'u_client.user_id as customer_id',
                        'u_client.name as customer_name',
                        'u_client.status as customer_status',
                        'manager.user_id as manager_id',
                        'manager.user_id as manager_id',
                        'manager.name as manager_name',
                        'manager.phone as manager_phone',
                    ]
                )
                ->leftJoin('orders_contractor as oc', 'o.orders_id', '=', 'oc.orders_id')
                ->leftJoin('user as manager', 'manager.user_id', '=', 'o.operator_id')
                ->leftJoin('user as u_client', 'u_client.user_id', '=', 'o.user_id')
                ->whereIn('o.orders_id', $ordersID)
                ->get()
                ->map(
                    function ($item) use ($user) {
                        $products = DB::table('orders_item')
                            ->select(
                                [
                                    'orders_item.orders_id',
                                    'orders_item.name',
                                    'orders_item.article',
                                    'orders_item.quantity',
                                    'orders_item.price',
                                    'item.typename',
                                    'item.image',
                                ]
                            )
                            ->where('orders_id', $item->orders_id)
                            ->leftJoin('item', 'item.item_id', '=', 'orders_item.item_id')
                            ->get()
                            ->map(
                                function ($item) {
                                    return [
                                        'id' => $item->orders_id,
                                        'name' => $item->name,
                                        'type' => $item->typename,
                                        'art' => $item->article,
                                        'image' => $item->image,
                                        'amount' => $item->quantity,
                                        'price' => $item->price,
                                    ];
                                }
                            );

                        return [
                            'id' => $item->orders_id,
                            'status' => $item->orders_status_id,
                            'id' => 'N',
                            'proposalPrice' => 'N',
                            'added' => $item->date_insert,
                            'comment' => $item->description,
                            'installDate' => $item->installdate,
                            'installTime' => $item->installtime,
                            'uninstallDate' => 'N',
                            'eventEnd' => $item->date_end_execution,
                            'eventEnd' => 'N',
                            'address' => $item->address,
                            'stage' => $item->stage,
                            'paymentSum' => $item->paymentSum,
                            'customer' => [
                                "category" => 'na',
                                "status" => $item->customer_status,
                                "orders" => 'na',
                                "ordersDone" => 'na',
                            ],
                            'manager' => [
                                'id' => $item->manager_id,
                                'name' => $item->manager_name,
                                'phone' => $item->manager_phone,
                            ],
                            'proposals' => [

                            ],
                            'products' => $products,
                        ];
                    }
                );
        }

        return response()->json(['success' => $orders], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function ordersForManagerWithoutPagination(Request $request)
    {
        $orderBuilder = DB::table('orders')
            ->groupBy('orders.orders_id')
            ->leftJoin('user as manager', 'manager.user_id', '=', 'orders.operator_id')
            ->leftJoinSub(
                DB::table('user')
                    ->select(
                        [
                            'name as customer_name',
                            'lastname as customer_lastname',
                            'secondname as customer_secondname',
                            'companyname',
                            'phone as customer_phone',
                            'email as customer_email',
                            'or.done as customer_done',
                            'or.ordered as customer_ordered',
                            'user.user_id as customer_user_id'
                        ]
                    )
                    ->leftJoinSub(
                        DB::table('orders')
                            ->select(
                                [
                                    DB::raw('max(user_id) as user_id'),
                                    DB::raw('sum(IF(FIND_IN_SET(orders_status_id, "'.implode(',', Order::doneStatus).'") > 0, 1, 0)) as done'),
                                    DB::raw('count(user_id) as ordered'),
                                ]
                            )
                            ->groupBy(['user_id']),
                        'or',
                        'or.user_id',
                        '=',
                        'user.user_id'
                    ), 'customer', 'customer.customer_user_id', '=', 'orders.user_id'
            )
            ->leftJoin('orders_item as oi',function($join) {
                 $join->on('oi.orders_id', '=', 'orders.orders_id');
             })
            ->leftJoin('orders_item_contractor as oic',function($join) {
                 $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
             })
            ->leftJoin('orders_status_comment', function ($join) {
                $join->on('orders_status_comment.orders_id', '=', 'orders.orders_id');
                $join->on('orders_status_comment.orders_status_id', '=', 'orders.orders_status_id');
            })
            ->select(
                [
                    'orders.orders_status_id',
                    'orders.user_id',
                    'orders.orders_id',
                    'orders.amount',
                    'orders.date_insert',
                    'orders.installdate',
                    'orders.installtime',
                    'orders.enddate',
                    'orders.endtime',
                    'orders.prepayment as paymentSum',
                    'orders.response_time',
                    'orders.description',
                    'orders.ocenka as rating',
                    'orders.feedback',
                    'orders.order_cancel_reason_id as declineId',
                    'orders.orders_status_id as status_id',
                    'orders_status_comment.id as com_id',
                    'orders_status_comment.comment as com_comment',
                    'orders.operator_id',
                    'orders.payment_type',
                    'orders.payment_status',
                    'orders.payment_date',
                    'orders.orders_payment_status_id as payment_status',
                    'orders.reserv_time as bookingDate',
                    'orders.call_date as callDate',
                    'orders.finalized',
                    'orders.rentprice as rent',
                    DB::raw('(orders.delivery + orders.rentpersonal - orders.rentpersonal_internal_our - orders.rentpersonal_internal_their - orders.delivery_internal_our - orders.delivery_internal_their - ifnull(orders.taxiConsumption,0)) as serviceProfit'),
                    DB::raw('(orders.delivery + orders.rentpersonal) as innerService'),
                    'orders.procatilo as rollup',
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, oic.price * oic.quantity, 0)) as outerRent'),
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, ifnull(oic.price_delivery, 0) + ifnull(oic.price_montag, 0), 0)) as outerService'),
                    'customer.*'
                ]);

        if ($query = $request->get('query')) {

            $orderBuilder->whereRaw(
                "
                (customer.customer_name like '%$query%' or customer.customer_secondname like '%$query%' or customer.customer_lastname like '%$query%' or customer.customer_email like '%$query%' or customer.customer_phone like '%$query%' or orders.orders_id = '$query')
            "
            );

        } else {

            if ($managersID = $request->get('managersID')) {
                $managersID = json_decode($managersID);
                $orderBuilder->whereIn('orders.operator_id', $managersID);
            }

            if ($request->has('dateFrom')) {
                $dateFrom = $request->get('dateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') >=?', [$dateFrom]);
            }

            $dateTo = $request->get('dateTo', false);

            if ($dateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') <=?', [$dateTo]);
            }

            if ($request->has('installDateFrom')) {
                $installDateFrom = $request->get('installDateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$installDateFrom]);
            }

            $installDateTo = $request->get('installDateTo', false);

            if ($installDateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$installDateTo]);
            }

            if ($request->has('uninstallDateFrom')) {
                $unInstallDateFrom = $request->get('uninstallDateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') >=?', [$unInstallDateFrom]);
            }

            $unInstallDateTo = $request->get('uninstallDateTo', false);

            if ($unInstallDateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') <=?', [$unInstallDateTo]);
            }


            if ($orderStatus = $request->get('orderStatus')) {
                $orderBuilder->whereIn('orders.orders_status_id', json_decode($orderStatus));
            }

            if ($excludeOrderStatus = $request->get('excludeOrderStatus')) {
                $orderBuilder->whereNotIn('orders.orders_status_id', json_decode($excludeOrderStatus));
            }

            if ($cancelReasonIds = $request->get('cancelReasonIds')) {
                $cancelReasonIds = json_decode($cancelReasonIds, true);
                $orderBuilder->whereIn('orders.order_cancel_reason_id', $cancelReasonIds);
            }
        }

        $items = [];
        $sortType = $request->has('sortType') ? $request->get('sortType') : 'orders_id';
        $sortDir = $request->has('sortDir') ? $request->get('sortDir') : 'desc';
        $orderBuilder->orderBy($sortType, $sortDir);
        $orderBuilder->chunk(10000, function ($chunkItems) use (&$items) {
            foreach ($chunkItems as $item) {
                if (!isset($items[$item->orders_id])) {
                    $items[$item->orders_id] = [
                        'id' => $item->orders_id,
                        'status' => $item->orders_status_id,
                        'price' => (int)$item->amount,
                        'ordered' => $item->date_insert,
                        'installDate' => $item->installdate,
                        'installTime' => $item->installtime,
                        'uninstallDate' => $item->enddate,
                        'uninstallTime' => $item->endtime,
                        'responseTime' => $item->response_time,
                        'rating' => $item->rating,
                        'feedback' => $item->feedback,
                        'declineId' => $item->declineId,
                        'company' => $item->companyname,
                        'comment' => $item->com_comment,
                        'managerId' => $item->operator_id,
                        'paymentStatus' => $item->payment_status,
                        'paymentType' => $item->payment_type,
                        'paymentDate' => $item->payment_date,
                        'paymentSum' => $item->paymentSum,
                        'bookingDate' => $item->bookingDate,
                        'callDate' => $item->callDate,
                        'finalized' => $item->finalized,
                        'innerService' => $item->innerService,
                        'innerServiceConsumption' => ($item->innerService - $item->serviceProfit),
                        'serviceProfit' => ($item->serviceProfit - $item->outerService),
                        'outerService' => $item->outerService,
                        'innerRent' => (is_numeric($item->rent) ? $item->rent : 0) - (is_numeric($item->outerRent) ? $item->outerRent : 0),
                        'outerRent' => $item->outerRent,
                        'rollup' => $item->rollup,
                        'customer' => [
                            'name' => $item->customer_name,
                            'lastName' => $item->customer_lastname,
                            'secondName' => $item->customer_secondname,
                            'email' => $item->customer_email,
                            'phone' => $item->customer_phone,
                            'done' => $item->customer_done,
                            'ordered' => $item->customer_ordered,
                            'user_id' => $item->customer_user_id,
                        ],
                        'tasks' => TodoManager::search(['orderId' => $item->orders_id])->select(['id'])->get(),
                    ];
                }

            }
        });

        $items = array_values($items);

        $return = [
            'success' => [
                'data' => $items,
            ],
        ];

        return response()->json($return, $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function ordersForManager(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::CONTRACTOR_ROLE_CODES))) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $orderBuilder = DB::table('orders')
            ->groupBy('orders.orders_id')
            ->select(
                [
                    'orders.orders_status_id',
                    'orders.user_id',
                    'orders.orders_id',
                    'orders.amount',
                    'orders.date_insert',
                    DB::raw('STR_TO_DATE(enddate, \'%Y-%m-%d\') as enddate'),
                    DB::raw('STR_TO_DATE(installdate, \'%Y-%m-%d\') as installdate'),
                    'orders.installtime',
                    'orders.endtime',
                    'orders.response_time',
                    'orders.ocenka as rating',
                    'orders.feedback',
                    'orders.description',
                    'orders.prepayment as paymentSum',
                    'orders.order_cancel_reason_id as declineId',
                    'orders.orders_status_id as status_id',
                    'operator_id',
                    'orders.payment_type',
                    'orders.payment_status',
                    'orders.payment_date',
                    'orders_status_comment.comment as com_comment',
                    'orders.payment_status as paymentStatus',
                    'orders.payment_type as paymentType',
                    'orders.payment_date as paymentDate',
                    'orders.reserv_time as bookingDate',
                    'orders.call_date as callDate',
                    'orders.finalized',
                    DB::raw('(orders.delivery + orders.rentpersonal - orders.rentpersonal_internal_our - orders.rentpersonal_internal_their - orders.delivery_internal_our - orders.delivery_internal_their - ifnull(orders.taxiConsumption,0)) as serviceProfit'),
                    'orders.rentprice as rent',
                    DB::raw('(orders.delivery + orders.rentpersonal) as innerService'),
                    'orders.procatilo as rollup',
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, oic.price * oic.quantity, 0)) as outerRent'),
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, ifnull(oic.price_delivery, 0) + ifnull(oic.price_montag, 0), 0)) as outerService'),
                    'customer.*'
                ]
            )
            ->leftJoin('orders_item as oi',function($join) {
                 $join->on('oi.orders_id', '=', 'orders.orders_id');
             })
            ->leftJoin('orders_item_contractor as oic',function($join) {
                 $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
             })
            ->leftJoinSub(
                DB::table('user')
                    ->select(
                        [
                            'name as customer_name',
                            'lastname as customer_lastname',
                            'secondname as customer_secondname',
                            'phone as customer_phone',
                            'email as customer_email',
                            'or.done as customer_done',
                            'or.ordered as customer_ordered',
                            'user.user_id as customer_id',
                            'user.companyname'
                        ]
                    )
                    ->leftJoinSub(
                        DB::table('orders')
                            ->select(
                                [
                                    DB::raw('max(user_id) as user_id'),
                                    DB::raw('sum(IF(FIND_IN_SET(orders_status_id, "'.implode(',', Order::doneStatus).'") > 0, 1, 0)) as done'),
                                    DB::raw('count(user_id) as ordered'),
                                ]
                            )
                            ->groupBy(['user_id']),
                        'or',
                        'or.user_id',
                        '=',
                        'user.user_id'
                    ), 'customer', 'customer.customer_id', '=', 'orders.user_id'
            )
            ->leftJoin('orders_status_comment', function ($join) {
                $join->on('orders_status_comment.orders_id', '=', 'orders.orders_id');
                $join->on('orders_status_comment.orders_status_id', '=', 'orders.orders_status_id');
            })
            ->leftJoin('user as c', 'c.user_id', '=', 'orders.user_id');

        if ($query = $request->get('query')) {
            $orderBuilder->whereRaw(
                "
                (c.name like '%$query%' or c.secondname like '%$query%' or c.lastname like '%$query%' or c.email like '%$query%' or c.phone like '%$query%' or orders.orders_id = '$query')
            "
            );

            if ($user->hasAnyRole(Role::CONTRACTOR_ROLE_CODES)) {

                $orderBuilder->whereIn('orders.orders_status_id', Order::contractorStatus);

            }
        } else {

            if ($managersID = $request->get('managersID')) {
                $managersID = json_decode($managersID);
                $orderBuilder->where('operator_id', $managersID);
            }

            if ($request->has('installDateFrom')) {
                $installDateFrom = $request->get('installDateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$installDateFrom]);
            }

            if ($request->has('dateFrom')) {
                $dateFrom = $request->get('dateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') >=?', [$dateFrom]);
            }

            $dateTo = $request->get('dateTo', false);

            if ($dateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') <=?', [$dateTo]);
            }

            $installDateTo = $request->get('installDateTo', false);

            if ($installDateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$installDateTo]);
            }

            if ($request->has('uninstallDateFrom')) {
                $unInstallDateFrom = $request->get('uninstallDateFrom');
                $orderBuilder->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') >=?', [$unInstallDateFrom]);
            }

            $unInstallDateTo = $request->get('uninstallDateTo', false);

            if ($unInstallDateTo) {
                $orderBuilder->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') <=?', [$unInstallDateTo]);
            }

            if ($orderStatus = $request->get('orderStatus')) {

                $orderStatus = json_decode($orderStatus);

                if ($user->hasAnyRole(Role::CONTRACTOR_ROLE_CODES)) {

                    $orderStatus = array_intersect($orderStatus, Order::contractorStatus);
                }

                $orderBuilder->whereIn('orders.orders_status_id', $orderStatus);
            } elseif ($user->hasAnyRole(Role::CONTRACTOR_ROLE_CODES)) {

                $orderBuilder->whereIn('orders.orders_status_id', Order::contractorStatus);

            }

            if ($excludeOrderStatus = $request->get('excludeOrderStatus')) {
                $orderBuilder->whereNotIn('orders.orders_status_id', json_decode($excludeOrderStatus));
            }

            if ($cancelReasonIds = $request->get('cancelReasonIds')) {
                $cancelReasonIds = json_decode($cancelReasonIds, true);
                $orderBuilder->whereIn('orders.order_cancel_reason_id', $cancelReasonIds);
            }
        }

        $sortType = 'orders.orders_id';
        $sortDir = $request->has('sortDir') ? $request->get('sortDir') : 'desc';

        $sortTypeMap = [
            'status' =>'orders.orders_status_id',
            'installdate' => 'installdate',
            'id' => 'orders.orders_id',
            'responseTime' => 'orders.responseTime',
            'uninstallDate' => 'enddate',
            'responseTime' => 'orders.response_time',
        ];

        if ($request->get('sortType') && isset($sortTypeMap[$request->get('sortType')])) {
            $sortType = $sortTypeMap[$request->get('sortType')];
        }

        $orderBuilder->orderBy($sortType, $sortDir);

        $totalCharge = $orderBuilder->sum('amount');
        $pagination = $orderBuilder->paginate(env('PAGINATION_COUNT_PER_PAGE'));

        $items = [];
        foreach ($pagination->items() as $item) {
            $items[] = [
                'id' => $item->orders_id,
                'status' => $item->orders_status_id,
                'id' => $item->orders_id,
                'price' => (int)$item->amount,
                'ordered' => $item->date_insert,
                'installDate' => $item->installdate,
                'installTime' => $item->installtime,
                'uninstallDate' => $item->enddate,
                'uninstallTime' => $item->endtime,
                'responseTime' => $item->response_time,
                'description' => null,
                'comment' => $item->com_comment,
                'rating' => $item->rating,
                'feedback' => $item->feedback,
                'declineId' => $item->declineId,
                'company' => $item->companyname,
                'managerId' => $item->operator_id,
                'paymentStatus' => $item->payment_status,
                'paymentType' => $item->payment_type,
                'paymentDate' => $item->payment_date,
                'paymentSum' => $item->paymentSum,
                'bookingDate' => $item->bookingDate,
                'callDate' => $item->callDate,
                'finalized' => $item->finalized,
                'innerService' => $item->innerService,
                'outerService' => $item->outerService,
                'innerServiceConsumption' => ($item->innerService - $item->serviceProfit),
                'serviceProfit' => ($item->serviceProfit - $item->outerService),
                'innerRent' => (is_numeric($item->rent) ? $item->rent : 0) - (is_numeric($item->outerRent) ? $item->outerRent : 0),
                'outerRent' => $item->outerRent,
                'rollup' => $item->rollup,
                'customer' => [
                    'name' => $item->customer_name,
                    'lastName' => $item->customer_lastname,
                    'secondName' => $item->customer_secondname,
                    'phone' => $item->customer_phone,
                    'email' => $item->customer_email,
                    'done' => $item->customer_done,
                    'ordered' => $item->customer_ordered,
                    'user_id' => $item->customer_id
                ],
                'tasks' => TodoManager::search(['orderId' => $item->orders_id])->select(['id'])->get(),
            ];

        }

        return response()->json(
            [
                'success' => [
                    'data' => $items,
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                    'total' => $pagination->total(),
                    'totalCharge' => $totalCharge
                ],
            ],
            $this->successStatus, [], JSON_NUMERIC_CHECK
        );
    }

    /**
     * customers api
     *
     * @return \Illuminate\Http\Response
     */
    public function ordersForCustomer(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        if (!$user->hasSupermanager() && !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $orderObject = new Order();
        $pagination = $orderObject->getList($request->all())
            ->paginate(env('PAGINATION_COUNT_PER_PAGE'));

        return response()->json(['success' => $pagination], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    /**
     * customers api
     *
     * @return \Illuminate\Http\Response
     */
    public function ordersForPid(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        if (!$user->hasPid() && !$user->hasSupermanager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $orderObject = new Order();
        $query = $orderObject->getList(array_merge($request->all()
            , ['pid' => $user->stand_id,
               'fields' => ['s.ordered as customerOrdered',
                    's.done as customerDone',
                    'o.phone as customerPhone',
                    'o.email as customerEmail',
                    'o.user_id as customerUser_id',
                    'customer.name as customerName',
                    'customer.lastname as customerLastname',
                    'customer.secondname as customerSecondname',
                ]
            ]));

        if ($request->get('full', 0)) {
            $result = ['data' => $orderObject->itemConvert($query->get())];
        } else {
            $pagination = $query->paginate(env('PAGINATION_COUNT_PER_PAGE'));
            $result = [
                    'data' => $orderObject->itemConvert($pagination->items()),
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                    'total' => $pagination->total(),
                ];
        }

        return response()->json(['success' => $result], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    /**
     * customers api
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        $id = $request->get('id', false);

        if (/*!($user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::CONTRACTOR_ROLE_CODES, Role::PID_ROLE_CODES)) && */!($id)) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $orderObject = new Order();
        $result = $orderObject->getItem($id);

        if (!$result
            || !(($user->hasAnyRole(Role::CONTRACTOR_ROLE_CODES) && in_array($result['status'], Order::contractorBuyStatus)) 
            || $user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::PID_ROLE_CODES))
            || $user->user_id == $result['customer']['id'])) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        return response()->json($result);
    }

    /**
     * order api
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        $id = $request->get('id', false);
        $orderObject = Order::find($id);

        if (!(($user->hasPid() || $user->hasManager() || $user->user_id == $orderObject->user_id) && $id)) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        if ($id == 'newOrder') {
            $t = new Order;
            $t->date_insert = Carbon::now();
            $t->user_id = isset($request->get('customer', [])['id']) ? $request->get('customer', [])['id'] : null;
            $t->installdate = '';
            $t->enddate = '';
            $t->cmf_site_id = config('common.siteId', 1);
            $t->save();
            $id = $t->orders_id;
        }

        $orderObject = Order::find($id);

        $fields = [];

        if ($orderObject->user_id && ($userObject = User::find(isset($request->get('customer', [])['id']) ? $request->get('customer', [])['id'] : $orderObject->user_id))) {
            $fieldMaps = [
                'status' => 'status',
                'category' => 'discounter_id',
                'name' => 'name',
                'lastName' => 'lastname',
                'secondName' => 'secondname',
                'email' => 'email',
                'phone' => 'phone',
                'company' => 'companyname',
                'description' => 'comment',
                'discount' => 'discount',
                'bonus' => 'bonus_points',
                'boss' => 'main_user',
            ];

            foreach ($request->get('customer', []) as $key=>$val) {

                if (isset($fieldMaps[$key])) {
                    $userObject[$fieldMaps[$key]] = $val;
                }
            }

            $fields['email'] = $userObject->email;
            $fields['fio'] = implode(' ', array_filter([$userObject->lastname, $userObject->name, $userObject->secondname]));
            $fields['phone'] = $userObject->phone;
            $fields['user_id'] = $userObject->user_id;
            $userObject->save();
        }


        $fieldMaps = [
            'eventStart' => 'event_start',
            'eventEnd' => 'event_finish',
            'callDate' => 'call_date',
            'bookingDate' => 'reserv_time',
            'paymentSum' => 'prepayment',
            'isPledgeReceived' => 'deposit_received',
            'isPledgeReturned' => 'deposit_returned',
            'paymentStatus' => 'orders_payment_status_id',
            'managerId' => 'operator_id',
            'status' => 'orders_status_id',
            'cars' => 'car_amount',
            'innerCars' => 'car_amount_our',
            'outerCars' => 'car_amount_their',
            'personnel' => 'worker_amount',
            'innerPersonnel' => 'worker_amount_our',
            'outerPersonnel' => 'worker_amount_their',
            'deliveryPrice' => 'delivery',
            'innerDeliveryPrice' => 'delivery_internal_our',
            'outerDeliveryPrice' => 'delivery_internal_their',
            'installPrice' => 'rentpersonal',
            'innerInstallPrice' => 'rentpersonal_internal_our',
            'outerInstallPrice' => 'rentpersonal_internal_their',
            'rollup' => 'procatilo',
            'foreignDelivery' => 'foreign_delivery',
            'selfDelivery' => 'is_pickup',
            'declineId' => 'order_cancel_reason_id',
            'noteLogistician' => 'description_logist',
            'noteStock' => 'description_warehouse',
            'noteDriver' => 'description_driver',
            'noteManager' => 'description_manager',
            'noteCustomer' => 'description',
            'address' => 'address',
            'installResponsible' => 'contact_name',
            'installResponsiblePhone' => 'contact_phone',
            'paymentType' => 'payment_type',
            'paymentDate' => 'payment_date',
            'daysChanged' => 'rent_day',
            'hoursChanged' => 'hour_rent_per_day',
            'mkadDistance' => 'mkadkm',
            'taxiConsumption' => 'taxiConsumption',
            'finalized' => 'finalized',
            'feedback' => 'feedback',
            'feedbackAnswer' => 'feedbackAnswer',
            'rating' => 'ocenka',
            'calculationDate' => 'settlement_date',
            'alternativesAgree' => 'alternative',
            'customerNeeds' => 'orders_goal_id',
            'eventType' => 'orders_event_type_id',
            'cmfSite' => 'cmf_site_id',
            'promocode' => 'promocode',
        ];

        foreach ($request->all() as $key=>$val) {
 
            if (isset($fieldMaps[$key])) {
                $fields[$fieldMaps[$key]] = $val;
            }
        }

        if (array_key_exists('cmf_site_id', $fields) && !$fields['cmf_site_id']) {

            unset($fields['cmf_site_id']);
        }

        if ($t = $request->get('stage', [])) {
            $fieldMaps = [
                'name' => 'stage',
                'address' => 'address',
                'elevatorSizes' => 'liftDimentions',
                'parking' => 'parking',
                'distance' => 'distance',
                'parkingPrice' => 'parkingPrice',
                'entryHeight' => 'entryHeight',
                'elevator' => 'elevator',
                'elevatorDistance' => 'elevatorDistance',
                'corridorWidth' => 'corridorWidth',
                'riseToFloor' => 'riseToFloor',
                'stepsWidth' => 'stepsWidth',
                'stepsTurnWidth' => 'stepsTurnWidth',
                'stageScheme' => 'stageScheme',
                'pass' => 'pass',
                'location' => 'location',
            ];

            foreach ($request->get('stage', []) as $key=>$val) {

                if (isset($fieldMaps[$key])) {
                    $fields[$fieldMaps[$key]] = $val;
                }
            }
        }

        if (($t = $request->get('installDate', false)) && ($t2 = $request->get('uninstallDate', false))) {
            $fields['renthours'] = round((strtotime($t2) - strtotime($t))/3600, 1);
        }

        if ($t = $request->get('installDate', false)) {
            $fields['installdate'] = date('Y-m-d', strtotime($t));
            $fields['installtime'] = date('H:i', strtotime($t));
        }

        if ($t = $request->get('uninstallDate', false)) {
            $fields['enddate'] = date('Y-m-d', strtotime($t));
            $fields['endtime'] = date('H:i', strtotime($t));
        }

        if ($t = $request->get('installDoneDate', false)) {

            $p = OrderLogist::firstOrNew(['orders_id' => $id, 'type' => DB::raw(3)]);
            $p->orders_id = $id;
            $p->type = 3;
            $p->finish = $t;
            $p->save();
        }

        if ($t = $request->get('payFromCashback', false)) {

            if (!($p = UserCashback::where('order_id', $id)->whereIn('type', [UserCashback::payment, UserCashback::paymentBlocked])->first())) {
                $p = new UserCashback();
            }

            $p->order_id = $id;
            $p->type = in_array($request->get('status', false), Order::doneStatus) ? UserCashback::payment : UserCashback::paymentBlocked;
            $p->price = $t;
            $p->user_id = $orderObject->user_id;
            $p->by_user_id = $user->user_id;
            $p->cdate = Carbon::now();
            $p->save();
        } elseif ($t === 0 || $t === '0') {

            UserCashback::where('order_id', $id)->where('user_id', $orderObject->user_id)->delete();
        }

        if ($t = $request->get('uninstallDoneDate', false)) {

            $p = OrderLogist::firstOrNew(['orders_id' => $id , 'type' => DB::raw(4)]);
            $p->orders_id = $id;
            $p->type = 4;
            $p->finish = $t;
            $p->save();
        }

        if (($t = $request->get('statusDescription', false)) && ($status = $request->get('status', false))) {

            $p = OrderStatusComment::firstOrNew(['orders_id' => $id , 'orders_status_id' => $status]);
            $p->orders_id = $id;
            $p->orders_status_id = $status;
            $p->comment = $t;
            $p->save();
        }

        if (($ts = $request->get('status', false))
            && $orderObject->orders_status_id != $ts) {

            $t = new OrderStatusLog;
            $t->orders_id = $id;
            $t->status_id = $ts;
            $t->user_id = $user->user_id;
            $t->cdate = Carbon::now();
            $t->save();
        }

        if ($orderObject->orders_payment_status_id != $request->get('paymentStatus', false)) {

            $t = new OrderPaymentStatusLog;
            $t->orders_id = $id;
            $t->status_id = $request->get('paymentStatus', false);
            $t->user_id = $user->user_id;
            $t->cdate = Carbon::now();
            $t->save();
        }

        $orderItems = [];

        if ($products = $request->get('products', false)) {

            foreach ($products as $v) {

                $p = OrdersItem::firstOrNew(['orders_id' => $id , 'item_id' => $v['id']]);
                $p->quantity = $v['amount'];
                $p->discount_percent_from_manager = $v['customDiscount'];
                $p->coefficient = $v['coefficient'];

                if (! $p->exists) {

                    $it = Item::find(['item_id' => $v['id'], 'cmf_site_id' => config('common.siteId', 1)]);

                    $p->orders_id = $id;
                    $p->item_id = $v['id'];
                } else {

                    foreach (['', 2, 3, 4] as $pos) {
                        $p['contractor'.$pos.'_id'] = null;
                        $p['contractor'.$pos.'_selected'] = null;
                        $p['price_contractor'.$pos] = null;
                        $p['quantity_contractor'.$pos] = null;
                        $p['delivery_contractor'.$pos] = null;
                        $p['install_contractor'.$pos] = null;
                    }
                }

                $p->save();
                $orderItems[$v['id']] = $p;
            }

            $is = OrdersItem::where('orders_id', $id)->whereNotIn('item_id', array_keys($orderItems))->delete();
        }

        $proposals = $positions = [];
        $fieldMapProposal = ['price' => 'price',
            'quantity' => 'amount',
            'state' => 'condition',
            'deposit' => 'pledge',
            'install' => 'install',
            'uninstall' => 'uninstall',
            'delivery' => 'deliveryIn',
            'pickup' => 'deliveryOut',
            'price_montag' => 'installPrice',
            'price_delivery' => 'deliveryPrice'];

        $fields['deposit'] = 0;

        if ($contractors = $request->get('proposals', false)) {

            foreach ($contractors as $contractor) {

                foreach ($contractor['products'] as $v) {

                    $proposalObject = null;

                    if (isset($orderItems[$v['forId']]) && ($proposalObject = OrderItemContractor::where([
                        'orders_item_id' => $orderItems[$v['forId']]->orders_item_id,
                        'contractor_id' => $v['contractorId']])->first())) {
                    } elseif(isset($orderItems[$v['forId']]) && ($v['checked'] || isset($contractor['visible']) && ! $contractor['visible'])) {

                        $proposalObject = new OrderItemContractor();
                        $proposalObject->orders_item_id = $orderItems[$v['forId']]->orders_item_id;
                        $proposalObject->contractor_id = $v['contractorId'];
                    }

                    if ($proposalObject) {

                        foreach ($fieldMapProposal as $key=>$val) {

                            if (array_key_exists($val, $v)) {

                                $proposalObject[$key] = $v[$val];
                            }
                        }

                        $proposalObject->status = isset($contractor['visible']) && ! $contractor['visible'] ? 2 : $v['checked'];
                        $proposalObject->save();

                        if ($proposalObject->status == 1) {

                            $fields['deposit'] += $proposalObject->deposit;
                        }

                        $proposals[$proposalObject->orders_item_contractor_id] = $proposalObject;

                        $pos = $positions[$v['forId']] = (isset($positions[$v['forId']]) ? ($positions[$v['forId']] == '' ? 2 : $positions[$v['forId']]+1) : '');
                        $orderItem = $orderItems[$v['forId']];
                        $orderItem['contractor'.$pos.'_id'] = $v['contractorId'];
                        $orderItem['contractor'.$pos.'_selected'] = $v['checked'];
                        $orderItem['price_contractor'.$pos] = $v['price'];
                        $orderItem['quantity_contractor'.$pos] = $v['amount'];
                        $orderItem['delivery_contractor'.$pos] = $v['deliveryPrice'];
                        $orderItem['install_contractor'.$pos] = $v['installPrice'];
                        $orderItem->save();
                    }
                }
            }
        }

        $fields['rentzalog'] = $fields['deposit'];

        $email = isset($fields['email']) ? $fields['email'] : $orderObject->email;
        $name = isset($fields['fio']) ? $fields['fio'] : $orderObject->fio;
        $send20 = (isset($fields['orders_status_id']) && $fields['orders_status_id'] == 35
                && $orderObject->orders_status_id != 35);
        $accrualCashback = isset($fields['orders_status_id']) && in_array($fields['orders_status_id'], [9, 44])
                && ! in_array($orderObject->orders_status_id, [9, 44]) && ! $request->get('payFromCashback', false);

        if (isset($fields['orders_payment_status_id']) && $fields['orders_payment_status_id'] == 5
                && $orderObject->orders_payment_status_id != 5) {

            $mail = Helper::mail(43, array(
                'to' => $email,
                'to_name' => $name,
                'mask' => [
                    'CLIENT_NAME' => $orderObject->user->name.' '.$orderObject->user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($email),
                    'ORDER_NUMBER' => $id,
                ],
            ));
        }

        if (isset($fields['orders_status_id']) && $fields['orders_status_id'] == 4
                && $orderObject->orders_status_id != 4) {

            $mail = Helper::mail(25, array(
                'to' => $email,
                'to_name' => $name,
                'mask' => [
                    'CLIENT_NAME' => $orderObject->user->name.' '.$orderObject->user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($email),
                    'ORDER_NUMBER' => $id,
                ],
            ));
        }

        if (isset($fields['orders_status_id']) && $fields['orders_status_id'] == 11
                && $orderObject->orders_status_id != 11) {

            $managers = User::whereHas('roles', function ($query){
                    $query->whereIn('code', Role::LOGIST_CODES);
                })
                ->get([
                    'email',
                    'name',
                    'lastname'
                ]);

            foreach ($managers as $managerObject) {

                $mail = Helper::mail(45, array(
                    'to' => $managerObject->email,
                    'to_name' => $managerObject->name.' '.$managerObject->lastname,
                    'mask' => [
                        'ORDER_ID' => $id,
                    ],
                ));
            }
        }

        if (isset($fields['orders_status_id']) && $fields['orders_status_id'] == 12
                && $orderObject->orders_status_id != 12) {

            $managerObject = User::find(isset($fields['operator_id']) ? $fields['operator_id'] : $orderObject->operator_id);

            $mail = Helper::mail(44, array(
                'to' => $managerObject->email.',hi@rguys.pro',
                'to_name' => $managerObject->name.' '.$managerObject->lastname,
                'mask' => [
                    'ORDER_ID' => $id,
                ],
            ));
        }

        $result = $orderObject->update($fields);
        $orderM = Order::getOrderById($id);
        $result = $orderObject->update(['rentprice' => $orderM['total']
            , 'amount' => $orderM['total'] + floatval($orderObject->rentpersonal) + floatval($orderObject->delivery) - $request->get('payFromCashback', 0)]);

        if ($accrualCashback) {

            $p = UserCashback::firstOrNew(['order_id' => $id, 'user_id' => $orderObject->user_id]);
            $p->order_id = $id;
            $p->type = UserCashback::accrual;
            $p->price = $orderM['availableCashback'];
            $p->user_id = $orderObject->user->main_user ? $orderObject->user->main_user : $orderObject->user_id;
            $p->by_user_id = $user->user_id;
            $p->cdate = Carbon::now();
            $p->save();
        }

        if ($send20) {

            $managerObject = User::find(isset($fields['operator_id']) ? $fields['operator_id'] : $orderObject->operator_id);
            $itemsHtml = '';

            if (isset($orderM['item'])) {

                 $itemsHtml = \View::make('mail.order', ['orderItem' => $orderM['item']])->render();

            }

            $mail = Helper::mail(20, array(
                'to' => $email,
                'to_name' => $name,
                'mask' => [
                    'CLIENT_NAME' => $orderObject->user->name.' '.$orderObject->user->secondname,
                    'CLIENT_EMAIL' => $email,
                    'CLIENT_EMAIL_CRYPT' => Helper::dsCrypt($email),
                    'ORDER_NUMBER' => $id,
                    'ORDER_DATE' => $orderObject->date_insert,
                    'CLIENT_PHONE' => $orderObject->phone,
                    'CLIENT_ADDRESS' => $orderObject->address,
                    'CLIENT_INFO' => $orderObject->description,
                    'MANAGER_NAME' => $managerObject->name.' '.$managerObject->lastname,
                    'MANAGER_PHONE' => $managerObject->phone,
                    'CASHBACK' => $orderM['availableCashback'],
                    'USER_TOVAR' => array($itemsHtml, 'html'),
                    'USER_INSTALLDATE' => $orderObject->installdate,
                    'USER_INSTALLTIME' => $orderObject->installtime,
                    'USER_ENDDATE' => $orderObject->enddate,
                    'USER_ENDTIME' => $orderObject->endtime,
                    'USER_RENTHOURS' => $orderObject->renthours,
                    'USER_RENTPRICE' => number_format($orderObject->rentprice, 0, '', ' '),
                    'USER_ONLYRENTPRICE' => number_format($orderObject->rentprice, 0, ',', ' '),
                    'USER_DELIVERY' => $orderObject->delivery,
                    'USER_CLIMB_VALUE' => $orderObject->climb,
                    'USER_ASSEMBLY_VALUE' => $orderObject->assembly,
                    'USER_RENTPERSONAL' => $orderObject->rentpersonal,
                    'USER_SERVICE_PRICE' => number_format(floatval($orderObject->rentpersonal) + floatval($orderObject->delivery), 0, ',', ' '),
                    'USER_RENTOVERWORK' => $orderObject->rentoverwork,
                    'USER_AMOUNT' => number_format($orderObject->amount, 0, '', ' '),
                    'PLEDGE' => number_format($orderM['pledge'] , 0, '', ' '),
                ],
            ));

            $smsText = str_replace(['[ORDERS_ID]', '[EMAIL]', '[CLIENT_NAME]'], [$id, Helper::dsCrypt($email), $orderObject->user->name.' '.$orderObject->user->secondname], CmfSetting::getString('SMS_FOR_ORDER_PROCESSED_BY_USER'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $orderObject->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Подтверждение менеджером заказа №'.$id.'  (статус обработан).', $smsUrl, $response);
        }

        $answer = ['success' => $result];

        if ($user->hasPid() || $user->hasManager()) {

            $answer['order'] = $orderObject->getItem($id);
        }

        return response()->json($answer);
    }

    public function statuses(Request $request)
    {
        $statuses = DB::table('orders_status')
            ->select([
                'orders_status_id as id',
                'name',
                'status',
                'color',
                'description',
            ])
            ->orderBy('ordering')
            ->get();

        return response()->json(['success' => $statuses], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }
}