<?php

namespace App\Http\Controllers\API;

use App\Exceptions\APIException;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use App\Order;
use App\OrdersItem;
use App\OrderItemContractor;
use App\OrderLogist;
use App\OrderStatusComment;
use App\OrderStatusLog;
use App\OrderPaymentStatusLog;
use App\TodoManager;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Helper;
use View;

class ReportController extends APIController
{

    /**
     * order api
     *
     * @return \Illuminate\Http\Response
     */
    public function getStat(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        if (!$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $orderObject = new Order();
        $filters = $request->all();

        return response()->json($orderObject->getStat($filters));
    }

    /**
     * warehouse api
     *
     * @return \Illuminate\Http\Response
     */
    public function getWarehouse(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        $roleId = Role::getIdByCode(Role::WAREHOUSE_CODES[0]);

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WAREHOUSE_CODES, Role::WORKSHOP))) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $orderLogistObject = new OrderLogist();
        $orderObject = new Order();
        $filters = array_merge($request->all(), [
            'fields' => [DB::raw('if(ol.status_warehouse>0, 1, 0) as status'),
            DB::raw('ol.status_warehouse as execUser')],
            'status' => [7, 8, 3, 9, 17, 18, 21, 20, 26, 24]]);

        $tasks = $orderLogistObject->getList($filters)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('ol.type', OrderLogist::INSTALL)
                              ->whereNotIn('ol.orders_id', function($query){
                                    $query->select('orders_id')
                                    ->from('orders_logist')
                                    ->where('type', OrderLogist::LOADING)
                                    ->whereNotNull('cdate')
                                    ->where('status', '>', 0)
                                    ->where('contractor_id', DB::raw(OrderLogist::RGUYS));
                                });
                    })
                    ->orWhere(function ($query) {
                        $query->where('ol.type', OrderLogist::UNINSTALL)
                              ->whereNotIn('ol.orders_id', function($query){
                                    $query->select('orders_id')
                                    ->from('orders_logist')
                                    ->where('type', OrderLogist::UNLOADING)
                                    ->whereNotNull('cdate')
                                    ->where('status', '>', 0)
                                    ->where('contractor_id', DB::raw(OrderLogist::RGUYS));
                                });
                    })
                    ->orWhere('ol.contractor_id', DB::raw(OrderLogist::RGUYS));
                })->get();

        $cache = $result = [];

        foreach ($tasks as &$task) {

            $task->exec = $orderLogistObject->getExecForTask($task, $roleId);

            if ($task->type == 'install') {

                $task->type = 'loading';
            } elseif ($task->type == 'uninstall') {

                $task->type = 'unloading';
            }

            if(! isset($result[$task->number.'-'.$task->type])) {


                $task->drivers = $orderLogistObject->getDriversForId($task->id);

                if (isset($cache[$task->number])) {

                    $task->order = $cache[$task->number];
                } else {

                    $task->order = $orderObject->getItem($task->number, 0);
                    $task->order['logist'] = OrderLogist::where('orders_id', $task->number)
                        ->whereIn('type', [OrderLogist::INSTALL, OrderLogist::UNINSTALL])
                        ->select([
                            DB::raw('CASE `type` 
                                        WHEN 3 THEN "install"
                                        WHEN 4 THEN "uninstall"
                                    END as `type`'),
                                'cdate as timeStart',
                                'edate as timeEnd',
                            ])
                        ->get();
                    $cache[$task->number] = $task->order;
                }

                $result[$task->number.'-'.$task->type] = $task;
            }
        }

        unset($task);

        return response()->json(['warehouseOperator' => User::whereHas('roles', function ($query){
                    $query->whereIn('code', Role::WAREHOUSE_CODES);
                })->select(['user_id as id', 'name', 'lastname'])->get(), 'tasks' => array_values($result)]);
    }

    /**
     * warehouse api
     *
     * @return \Illuminate\Http\Response
     */
    public function updateWarehouse(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        $id = $request->get('id', false);
        $roleId = Role::getIdByCode(Role::WAREHOUSE_CODES[0]);
        $orderLogistObject = OrderLogist::find($id);

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WAREHOUSE_CODES)) && $id) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $orderLogistObject->status_warehouse = $request->get('status', 0) ? $user->user_id : 0;
        $orderLogistObject->comment_warehouse = $request->get('comment', '');
        $orderLogistObject->save();

        if ($exec = $request->get('exec', [])) {

            foreach ($exec as $key=>$val) {

                DB::table('orders_logist_exec')
                    ->updateOrInsert([
                            'orders_logist_id' => $id,
                            //'user_id' => $user->user_id,
                            'role_id' => $roleId,
                            'item_id' => $val['id'],
                            'parent_id' => isset($val['parent_id']) ? $val['parent_id'] : 0,
                        ], [
                            'orders_logist_id' => $id,
                            'user_id' => $user->user_id,
                            'role_id' => $roleId,
                            'cdate' => Carbon::now(),
                            'item_id' => $val['id'],
                            'parent_id' => isset($val['parent_id']) ? $val['parent_id'] : 0,
                            'quantity' => $val['quantity'],
                            'damaged' => isset($val['damaged']) ? $val['damaged'] : 0,
                        ]
                    );
            }
        }

        if ($orderLogistObject->status_warehouse > 0
            && ($orderObject = Order::find($orderLogistObject->orders_id))) {

            if ($orderLogistObject->type == OrderLogist::UNLOADING || $orderLogistObject->type == OrderLogist::UNINSTALL) {

                $orderObject->orders_status_id = 3;
                $orderObject->save();

                $t = new OrderStatusLog;
                $t->orders_id = $orderLogistObject->orders_id;
                $t->status_id = $orderObject->orders_status_id;
                $t->user_id = $user->user_id;
                $t->cdate = Carbon::now();
                $t->save();

                $itemsHtml = $exec ? \View::make('mail.warehouse', ['orderItem' => $orderLogistObject->getExecForTask($orderLogistObject, $roleId)])->render() : null;

                if ($orderLogistObject->comment_warehouse || $itemsHtml) {

                    $mail = Helper::mail(47, array(
                        'to' => $orderObject->email,
                        'to_name' => $orderObject->fio,
                        'mask' => [
                            'CLIENT_NAME' => $orderObject->fio,
                            'ORDER_ID' => $orderLogistObject->orders_id,
                            'DESCRIPTION' => $orderLogistObject->comment_warehouse,
                            'CLIENT_EMAIL' => Helper::dsCrypt($orderObject->email),
                            'ITEMS' => array($itemsHtml, 'html'),
                        ],
                    ));
                }
            }
        }

        $request->request->add(['query' => $orderLogistObject->orders_id]);

        return $this->getWarehouse($request);
    }

}