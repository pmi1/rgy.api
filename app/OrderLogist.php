<?php

namespace App;

use App\Order;
use App\Contractor;
use App\OrderLogistExec;
use App\OrderLogistPhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderLogist extends Model
{
    const WAREHOUSE_DONE = 2;
    const LOADING = 1;
    const UNLOADING = 2;
    const INSTALL = 3;
    const UNINSTALL = 4;
    const ASSIGNMENT = 5;
    const RGUYS = 3;

    public $table = 'orders_logist';

    protected $fillable = ['finish', 'status', 'comment_warehouse', 'status_warehouse'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id',  'orders_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id',  'contractor_id')->where('contractor.cmf_site_id', config('common.siteId', 1));
    }

    public function helpers()
    {
        return $this->hasMany(OrderLogistHelper::class, 'orders_logist_id', 'id');
    }

    public function drivers()
    {
        return $this->hasMany(OrderLogistDriver::class, 'orders_logist_id', 'id');
    }

    public function exec()
    {
        return $this->hasMany(OrderLogistExec::class, 'orders_logist_id', 'id');
    }

    public function execPhoto()
    {
        return $this->hasMany(OrderLogistExecPhoto::class, 'orders_logist_id', 'id');
    }

    public function getList($options)
    {
        $fields = [
                'id',
                DB::raw('CASE ol.`type` WHEN 1 THEN "loading"
                        WHEN 2 THEN "unloading"
                        WHEN 3 THEN "install"
                        WHEN 4 THEN "uninstall"
                        WHEN 5 THEN "mission"
                    END as `type`'),
                'ol.orders_id as number',
                'ol.cdate as timeStart',
                'ol.edate as timeEnd',
                'ol.status',
                'ol.comment',
                'ol.comment_warehouse as commentWarehouse',
                'ol.comment_for_warehouse as commentForWarehouse',
                'ol.contact',
                'ol.contractor_id as contractor',
                DB::raw('if(length(ol.address), ol.address, o.address) as address')];

        if (isset($options['fields']) && $options['fields']) {
            $fields = array_merge($fields, $options['fields']);
        }

        $sql = DB::table('orders_logist as ol')
            ->select($fields)
            ->leftJoin('orders as o',function($join) {
                 $join->on('ol.orders_id', '=', 'o.orders_id');
             })
            ->whereNotNull('ol.cdate')
            ->whereNotNull('o.orders_id')
            ->where('ol.status', '>', 0)
            ->where('o.cmf_site_id', config('common.siteId', 1))
            ->orderBy('ol.cdate')
            ->groupBy('ol.id');

        if (isset($options['from']) && $options['from']) {

            $sql->where('ol.cdate', '>=', $options['from']);
        }

        if (isset($options['to']) && $options['to']) {

            $sql->where('ol.cdate', '<=', date('Y-m-d 23:59', strtotime($options['to'])));
        }

        if (isset($options['type']) && $options['type']) {

            $sql->where('ol.type', $options['type']);
        }

        if (isset($options['user']) && $options['user']) {

            $sql->where('o.user_id ', $options['user']);
        }

        if (isset($options['query']) && $options['query']) {

            $sql->where('ol.orders_id', $options['query']);
        }

        if (isset($options['status']) && $options['status']) {

            $sql->where(function ($query) use($options) {
                $query->whereNull('o.orders_status_id')
                    ->orWhereIn('o.orders_status_id', $options['status']);
            });
        }

        return $sql;
    }

    public function getDriversForId($id)
    {
        $result = DB::table('orders_logist_driver as old')
            ->select([
                'c.name',
                DB::raw('ifnull(c.nomer, old.car) as car'),
                'old.main',
                'u.phone',
                DB::raw('if(u.lastname IS NULL, old.driver, concat(u.name, " ", u.lastname)) as driver')])
            ->leftJoin('car as c',function($join) {
                 $join->on('old.car', '=', DB::raw('CONVERT(c.car_id, CHAR)'));
             })
            ->leftJoin('user as u',function($join) {
                 $join->on('old.driver', '=', DB::raw('CONVERT(u.user_id, CHAR)'));
             })
            ->where('old.orders_logist_id', $id)
            ->get();

        return $result;
    }

    public function getHelpersForId($id)
    {
        $result = DB::table('orders_logist_helper as old')
            ->select([
                'old.main',
                'u.phone',
                DB::raw('if(u.lastname IS NULL, old.driver, concat(u.name, " ", u.lastname)) as helper')])
            ->leftJoin('user as u',function($join) {
                 $join->on('old.driver', '=', DB::raw('CONVERT(u.user_id, CHAR)'));
             })
            ->where('old.orders_logist_id', $id)
            ->get();

        return $result;
    }

    public function getExecForTask($task, $roleId)
    {
        if ($task->type == 'unloading' || $task->type == self::UNLOADING
            || $task->type == 'uninstall' || $task->type == self::UNINSTALL) {

            $forId = DB::table('orders_logist as ol')
                ->select([
                    'ol2.id'
                ])
                ->leftJoin('orders_logist as ol2',function($join) use($task) {
                     $join->on('ol2.orders_id', '=', 'ol.orders_id')
                        ->whereIn('ol2.type', [self::LOADING, self::INSTALL]);
                     $join->on('ol2.status_warehouse', '>', DB::raw(0));
                     $join->on('ol2.id', '<>', 'ol.id')
                        ->where(function ($query) {
                            $query->where('ol2.type', self::INSTALL)
                                ->orWhere('ol2.contractor_id', DB::raw(OrderLogist::RGUYS));
                        });
                 })
                ->where('ol.id', $task->id)
                ->first();

            if ($forId) {

                $result = DB::table('orders_logist_exec as ole')
                    ->select([
                        'ole.id',
                        'old.quantity',
                        'old.user_id as userId',
                        'ole.item_id as productId',
                        'old.damaged',
                        'old.cdate as date',
                        'ole.quantity as issuedQuantity',
                        'i.itemtype',
                        'i.typename',
                        'i.article',
                        'old.item_id',
                        'ole.parent_id as parentId',
                        'i2.itemtype as parentItemtype',
                        'i2.typename as parentTypename',
                        'i2.article as parentArticle',
                        'ole.cdate',
                    ])
                    ->leftJoin('item as i',function($join) {
                         $join->on('i.item_id', '=', 'ole.item_id');
                         $join->on('i.cmf_site_id', DB::raw(1));
                     })
                    ->leftJoin('item as i2',function($join) {
                         $join->on('i2.item_id', '=', 'ole.parent_id');
                         $join->on('i2.cmf_site_id', DB::raw(1));
                     })
                    ->leftJoin('orders_logist_exec as old',function($join) use($task, $roleId) {
                         $join->on('ole.item_id', '=', 'old.item_id');
                         $join->on('ole.parent_id', '=', 'old.parent_id');
                         $join->on('old.role_id', DB::raw($roleId));
                         $join->on('old.orders_logist_id', DB::raw($task->id));
                     })
                    ->where('ole.role_id', $roleId)
                    ->where('ole.orders_logist_id', $forId->id)
                    ->get();
            } else {

                $result = 'loading task not found';
            }
        } else {

            $result = DB::table('orders_logist_exec as old')
                ->select([
                    'quantity',
                    'item_id as productId',
                    'user_id as userId',
                    'parent_id as parentId',
                    'cdate'
                ])
                ->where('old.role_id', $roleId)
                ->where('old.orders_logist_id', $task->id)
                ->get();
        }

        return $result;
    }
}