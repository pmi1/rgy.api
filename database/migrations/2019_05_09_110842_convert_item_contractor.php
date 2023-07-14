<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertItemContractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT IGNORE INTO `item_contractor` (`item_id`, `contractor_id`) 
            SELECT `item_id`, `contractor_id` FROM item WHERE contractor_id>0");
        DB::statement("INSERT IGNORE INTO `item_contractor` (`item_id`, `contractor_id`) 
            SELECT `item_id`, `contractor2_id` FROM item WHERE contractor2_id>0");
        DB::statement("INSERT IGNORE INTO `item_contractor` (`item_id`, `contractor_id`) 
            SELECT `item_id`, `contractor3_id` FROM item WHERE contractor3_id>0");
        DB::statement("INSERT IGNORE INTO `item_contractor` (`item_id`, `contractor_id`) 
            SELECT `item_id`, `contractor4_id` FROM item WHERE contractor4_id>0");

        DB::table('orders_item_contractor')->insertUsing(['orders_item_id','contractor_id', 'price', 'price_delivery', 'price_montag', 'quantity', 'state']
            , DB::table('orders_item')->select(['orders_item_id','contractor_id', DB::raw('if(price_contractor, CAST(price_contractor AS DECIMAL(10,2)), price) as price'), 'delivery_contractor', 'install_contractor', DB::raw('if(quantity_contractor, CAST(quantity_contractor AS UNSIGNED), quantity) as q'), 'condition'])->where('contractor_id', '>', '0')->where('contractor_selected', '>', '0'));
        DB::table('orders_item_contractor')->insertUsing(['orders_item_id','contractor_id', 'price', 'price_delivery', 'price_montag', 'quantity', 'state']
            , DB::table('orders_item')->select(array('orders_item_id','contractor2_id', DB::raw('if(price_contractor2, CAST(price_contractor2 AS DECIMAL(10,2)), price) as price'), 'delivery_contractor2', 'install_contractor2', DB::raw('if(quantity_contractor2, CAST(quantity_contractor2 AS UNSIGNED), quantity) as q'), 'condition'))->where('contractor2_id', '>', '0')->where('contractor2_selected', '>', '0'));
        DB::table('orders_item_contractor')->insertUsing(['orders_item_id','contractor_id', 'price', 'price_delivery', 'price_montag', 'quantity', 'state']
            , DB::table('orders_item')->select(array('orders_item_id','contractor3_id', DB::raw('if(price_contractor3, CAST(price_contractor3 AS DECIMAL(10,2)), price) as price'), 'delivery_contractor3', 'install_contractor3', DB::raw('if(quantity_contractor3, CAST(quantity_contractor3 AS UNSIGNED), quantity) as q'), 'condition'))->where('contractor3_id', '>', '0')->where('contractor3_selected', '>', '0'));
        DB::table('orders_item_contractor')->insertUsing(['orders_item_id','contractor_id', 'price', 'price_delivery', 'price_montag', 'quantity', 'state']
            , DB::table('orders_item')->select(array('orders_item_id','contractor4_id', DB::raw('if(price_contractor4, CAST(price_contractor4 AS DECIMAL(10,2)), price) as price'), 'delivery_contractor4', 'install_contractor4', DB::raw('if(quantity_contractor4, CAST(quantity_contractor4 AS UNSIGNED), quantity) as q'), 'condition'))->where('contractor4_id', '>', '0')->where('contractor4_selected', '>', '0'));
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
