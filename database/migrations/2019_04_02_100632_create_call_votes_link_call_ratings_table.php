<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallVotesLinkCallRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_votes_link_call_ratings', function (Blueprint $table) {
            $table->unsignedInteger('call_vote_id');
            $table->unsignedInteger('rating_id');
            $table->integer('value');
            $table->unsignedInteger('manager_id');
            $table->increments('id');
            $table->timestamps();
            $table->unique(['call_vote_id', 'rating_id'], 'vote_id_rating_id');
        });

        Schema::table('call_votes_link_call_ratings', function (Blueprint $table){
            $table->foreign('manager_id')
                ->references('user_id')
                ->on('user')
                ->onDelete('cascade');
        });

        Schema::table('call_votes_link_call_ratings', function (Blueprint $table){
            $table->foreign('call_vote_id')
                ->references('id')
                ->on('call_votes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_link_call_ratings');

    }
}
