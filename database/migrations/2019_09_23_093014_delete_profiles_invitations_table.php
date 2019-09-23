<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteProfilesInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('invitations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('invitations', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('pin');
            $table->unsignedInteger('player_id')->nullable();
            $table->datetime('valid_until');
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('players');
        });

        Schema::create('profiles', function (Blueprint $table)
        {
            $table->increments('id');

            $table->unsignedInteger('player_id')->nullable();
            $table->foreign('player_id')->references('id')->on('players');

            $table->boolean('queued')->nullable();

            $table->integer('points')->nullable();
            $table->float('pointsPerGame', 3, 2)->nullable();
            $table->float('pointsPerWin', 3, 2)->nullable();
            $table->float('pointsPerLose', 3, 2)->nullable();

            $table->integer('games')->nullable();
            $table->integer('gamesThisMonth')->nullable();
            $table->float('gamesPerDay', 3, 2)->nullable();
            $table->integer('gamesWon')->nullable();
            $table->integer('gamesLost')->nullable();
            $table->float('winrate', 4, 1)->nullable();

            $table->integer('soli')->nullable();
            $table->integer('soliWon')->nullable();
            $table->integer('soliLost')->nullable();
            $table->integer('soloRate')->nullable();
            $table->float('soloWinrate', 4, 1)->nullable();
            $table->integer('soloPoints')->nullable();

            $table->integer('mostGamesDay')->nullable();
            $table->timestamp('mostGamesDayDate')->nullable();
            $table->integer('mostGamesMonth')->nullable();
            $table->timestamp('mostGamesMonthDate')->nullable();

            $table->integer('highestPoints')->nullable();
            $table->timestamp('highestPointsDate')->nullable();
            $table->integer('lowestPoints')->nullable();
            $table->timestamp('lowestPointsDate')->nullable();

            $table->integer('winStreak')->nullable();
            $table->timestamp('winStreakStart')->nullable();
            $table->timestamp('winStreakEnd')->nullable();
            $table->integer('loseStreak')->nullable();
            $table->timestamp('loseStreakStart')->nullable();
            $table->timestamp('loseStreakEnd')->nullable();


            $table->timestamps();
        });
    }
}
