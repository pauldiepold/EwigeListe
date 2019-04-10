<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('player_id')->nullable();
            $table->foreign('player_id')->references('id')->on('players');

            $table->integer('points')->nullable();
            $table->float('pointsPerGame', 3, 2)->nullable();
            $table->float('pointsPerWin', 3, 2)->nullable();
            $table->float('pointsPerLose', 3, 2)->nullable();

            $table->integer('games')->nullable();
            $table->integer('gamesWon')->nullable();
            $table->integer('gamesLost')->nullable();
            $table->float('winrate', 4, 1)->nullable();

            $table->integer('soli')->nullable();
            $table->integer('soliWon')->nullable();
            $table->integer('soliLost')->nullable();
            $table->float('soloWinrate', 4, 1)->nullable();
            $table->integer('soloPoints')->nullable();

            $table->date('mostGamesDay')->nullable();
            $table->integer('mostGamesDayCount')->nullable();
            $table->integer('mostGamesMonth')->nullable();

            $table->integer('highestPoints')->nullable();
            $table->integer('lowestPoints')->nullable();

            $table->integer('winStreak')->nullable();
            $table->integer('loseStreak')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
