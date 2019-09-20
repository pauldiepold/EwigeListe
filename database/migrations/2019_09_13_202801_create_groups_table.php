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
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('players');
        });

        Schema::create('group_player', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('group_id');
            $table->timestamps();

            $table->unique(['player_id', 'group_id']);

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::create('group_round', function (Blueprint $table) {
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
        Schema::dropIfExists('group_player');
        Schema::dropIfExists('group_round');
        Schema::dropIfExists('groups');
    }
}
