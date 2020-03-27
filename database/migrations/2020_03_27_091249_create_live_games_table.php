<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_games', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('live_round_id');

            $table->boolean('closed');

            $table->timestamps();

            $table->foreign('live_round_id')
                ->references('id')
                ->on('live_rounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('live_games');
    }
}
