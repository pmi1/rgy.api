<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FillCmfRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mapRoles = [
            1 => 'developer',
            16 => 'admin',
            17 => 'editor',
            18 => 'ordersmanager',
            19 => 'banneroperator',
            20 => 'useradminpanel',
            21 => 'userclient',
            22 => 'userdisallowpart',
            23 => 'logistician',
            24 => 'warehouseoperator',
            25 => 'accountant',
            26 => 'supermanager',
            27 => 'driver',
            28 => 'helper',
            29 => 'contractor',
            30 => 'driverinhouse',
            31 => 'storekeeper',

        ];
        $results = DB::table('cmf_role')->select('cmf_role_id','code')->get();

        foreach ($results as $result){
            if (isset($mapRoles[$result->cmf_role_id])) {
                DB::table('cmf_role')
                    ->where('cmf_role_id',$result->cmf_role_id)
                    ->update([
                        "code" => $mapRoles[$result->cmf_role_id]
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
