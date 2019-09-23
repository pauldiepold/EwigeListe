<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndiceToGamePlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_player', function (Blueprint $table) {
            $table->unique(['game_id', 'player_id']);

            $table->index('won');
            $table->index('soloist');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_player', function (Blueprint $table) {
            $table->dropIndex('game_player_won_index');
            $table->dropIndex('game_player_soloist_index');
        });
    }
}
