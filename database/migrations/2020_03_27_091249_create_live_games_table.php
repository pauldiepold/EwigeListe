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

            //$table->boolean('test');
            //$table->integer('stich');
            //$table->boolean('herz10');
            $table->string('spielerIDs');
            $table->string('spielerIDsInaktiv');
            $table->string('spielerIndize');
            $table->text('spieler0');
            $table->text('spieler1');
            $table->text('spieler2');
            $table->text('spieler3');

            $table->integer('phase');

            $table->integer('vorhand');
            $table->integer('dran');

            $table->text('letzterStich');
            $table->text('aktuellerStich');

            $table->boolean('beendet');

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
