<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('players');
        });

        Schema::create('profiles', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('group_id');

            $table->boolean('queued')->nullable();
            $table->integer('points')->nullable();
            $table->float('pointsPerGame', 4, 2)->nullable();
            $table->float('pointsPerWin', 4, 2)->nullable();
            $table->float('pointsPerLose', 4, 2)->nullable();

            $table->integer('games')->nullable();
            $table->integer('gamesThisMonth')->nullable();
            $table->float('gamesPerDay', 5, 2)->nullable();
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

            $table->unique(['player_id', 'group_id']);

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::create('group_round', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('round_id');
            $table->unsignedInteger('group_id');
            $table->timestamps();

            $table->unique(['round_id', 'group_id']);

            $table->foreign('round_id')->references('id')->on('rounds');
            $table->foreign('group_id')->references('id')->on('groups');
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
        Schema::dropIfExists('group_round');
        Schema::dropIfExists('groups');
    }
}
