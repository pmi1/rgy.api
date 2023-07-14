<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('item_contractor', function (Blueprint $table) {
            $table->double('deposit', 8, 2)->nullable();
            $table->unsignedInteger('cashback')->nullable();
            $table->unsignedInteger('bonus')->nullable();
            $table->timestamps();
        });
        DB::statement(" INSERT INTO `item_contractor` (`item_id`, `contractor_id`, `price`, `available_quantity`, `deposit`, `cashback`, `bonus`)
            (SELECT `item_id`, `contractor_id`, `price`, if(availablecount='', NULL, CAST(SUBSTRING_INDEX(availablecount,'-',-1) AS UNSIGNED)), `pricebuy`, `bonus`, `bonusPay` 
FROM item WHERE contractor_id = 3 or contractor2_id=3 or contractor3_id=3 or contractor4_id=3) 
ON DUPLICATE KEY UPDATE `item_contractor`.`price` = `item`.`price`, `item_contractor`.`deposit` = `item`.`pricebuy`
, `item_contractor`.`available_quantity` = if(availablecount='', NULL, CAST(SUBSTRING_INDEX(availablecount,'-',-1) AS UNSIGNED)), `item_contractor`.`cashback` = `item`.`bonus`, `item_contractor`.`bonus` = `item`.`bonusPay`;");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('item_contractor', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
