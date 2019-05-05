<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeOnForeignPlayerRound extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_round', function(Blueprint $table)
        {
            $table->dropForeign('player_round_round_id_foreign');
            $table->foreign('round_id')
                ->references('id')
                ->on('rounds')
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
        Schema::table('player_round', function(Blueprint $table)
        {
            $table->dropForeign('player_round_round_id_foreign');
            $table->foreign('round_id')
                ->references('id')
                ->on('rounds');
        });
    }
}
