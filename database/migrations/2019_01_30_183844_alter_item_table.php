<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->integer('pledge')->nullable();
            $table->integer('condition')->nullable();
            $table->integer('one_car')->nullable();
            $table->integer('stock_id')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('bonusPay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
            $table->dropColumn('pledge');
            $table->dropColumn('condition');
            $table->dropColumn('one_car');
            $table->dropColumn('stock_id');
            $table->dropColumn('one_car');
            $table->dropColumn('bonus');
            $table->dropColumn('bonusPay');
        });
    }
}
