<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Catalogue extends Model
{
    public $primaryKey = 'catalogue_id';

    public $table = 'catalogue';

    public function getList($options)
    {
        $sql = DB::table('catalogue as t')
            ->select([
                    't.catalogue_id as id',
                    't.parent_id as parentId',
                    't.name',
                    't.item_count as products',
                    't.real_item_count as active',
                    isset($options['pid']) && $options['pid'] ? DB::raw('cp.item_id>0 as status') : 't.status'
                ])
            ->where('t.cmf_site_id', config('common.siteId', 1))
            ->where('t.realstatus', 1)
            ->where('t.real_item_count', '>', 0)
            ->orderBy('ordering', 'asc');

        if (isset($options['pid']) && $options['pid']) {
            $sql->leftJoin('link_item_catalogue as cp', function($join) use ($options)
                         {
                             $join->on('cp.catalogue_id', '=', 't.catalogue_id');
                             $join->on('cp.cmf_site_id', '=', 't.cmf_site_id');
                             $join->on('cp.item_id', DB::raw($options['pid']));
                         })
                ->where('t.status', 1);
        }

        return $sql;
    }

}