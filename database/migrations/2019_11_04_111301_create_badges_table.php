<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('group_id');

            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('month');

            $table->char('type');
            $table->integer('value')->nullable();

            $table->timestamps();

            $table->foreign('player_id')
                ->references('id')
                ->on('players');

            $table->foreign('group_id')
                ->references('id')
                ->on('groups');

            $table->unique(['player_id', 'group_id', 'year', 'month']);

            $table->index('player_id');
            $table->index('group_id');
            $table->index('year');
            $table->index('month');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badges');
    }
}
