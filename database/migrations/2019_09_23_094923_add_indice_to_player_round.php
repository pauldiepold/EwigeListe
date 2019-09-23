<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndiceToPlayerRound extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_round', function (Blueprint $table) {
            $table->unique(['player_id', 'round_id']);

            $table->index('index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_round', function (Blueprint $table) {
            $table->dropIndex('player_round_index_index');
        });
    }
}
