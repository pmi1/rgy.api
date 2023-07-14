<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderItemContractorAddTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders_item_contractor', function (Blueprint $table) {
            $table->timestamps();
        });

        DB::statement("ALTER TABLE orders_item_contractor CHANGE pickup pickup  tinyint(3) unsigned NULL");
        DB::unprepared("DROP TRIGGER IF EXISTS `orders_item_ad`");
        DB::unprepared("
CREATE TRIGGER `orders_item_ad` AFTER DELETE ON `orders_item` FOR EACH ROW
begin
select orderComplect(OLD.orders_id) into @r;
delete from orders_item_contractor where orders_item_id = OLD.orders_item_id;
end");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('orders_item_contractor', function (Blueprint $table) {
            $table->dropTimestamps();
        });

    }
}
