<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RgContractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_contractor', function (Blueprint $table) {
            $table->double('deposit')->nullable();
        });
        //
        DB::statement(" INSERT INTO `item_contractor` (`item_id`, `contractor_id`, `price`, `available_quantity`, `deposit`)
            (SELECT `item_id`, `contractor_id`, `price`, if(availablecount='', NULL, CAST(SUBSTRING_INDEX(availablecount,'-',-1) AS UNSIGNED)), `pricebuy` 
FROM item WHERE contractor_id = 3 or contractor2_id=3 or contractor3_id=3 or contractor4_id=3) 
ON DUPLICATE KEY UPDATE `item_contractor`.`price` = `item`.`price`, `item_contractor`.`deposit` = `item`.`pricebuy`
, `item_contractor`.`available_quantity` = if(availablecount='', NULL, CAST(SUBSTRING_INDEX(availablecount,'-',-1) AS UNSIGNED));");

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
