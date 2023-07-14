<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCatItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cat_item', function (Blueprint $table) {
            $table->foreign('item_id', 'cat_item_item_id_foreign')->references('item_id')->on('item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cat_item', function (Blueprint $table) {
            $table->dropForeign('cat_item_item_id_foreign');
        });
    }
}
