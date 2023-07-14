<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTabsForItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::table('cmf_script')
            ->where('cmf_script_id', 427)
            ->where('cmf_site_id', 1)
            ->update(['status' => 0, 'realstatus' => 0]);
        DB::table('cmf_script')
            ->where('cmf_script_id', 424)
            ->where('cmf_site_id', 1)
            ->update(['name' => 'Подрядчики']);

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
