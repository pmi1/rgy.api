<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetManagerFlagUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $managerMap = [
            14692,
            14693,
            15080,
            15099,
            15100,
            15101,
            15689,
            15690,
            15691,
            15692,
            15693,
            16939,
            19559
        ];

        DB::table('user')
            ->whereIn('user_id', $managerMap)
            ->update(['is_manager' => 'yes']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
