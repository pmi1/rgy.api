<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterItemIdColorItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('color_item', function (Blueprint $table) {
            $table->renameColumn('item_id', 'item_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('color_item', function (Blueprint $table) {
            $table->renameColumn('item_item_id', 'item_id');
        });
    }
}
