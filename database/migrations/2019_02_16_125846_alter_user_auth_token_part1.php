<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserAuthTokenPart1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth_token', function (Blueprint $table) {
            $table->dropPrimary('PRIMARY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_auth_token', function (Blueprint $table) {
            $table->dropPrimary();
            $table->primary(['user_id', 'code']);
        });
    }
}
