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

            $table->string('spielerIDsInaktiv');
            $table->text('spieler0');
            $table->text('spieler1');
            $table->text('spieler2');
            $table->text('spieler3');

            $table->text('anzeige');

            $table->integer('phase');
            $table->integer('stichNr');

            $table->integer('vorhand');
            $table->integer('dran');

            $table->string('spieltyp');

            $table->text('letzterStich');
            $table->text('aktuellerStich');

            $table->boolean('gewinntRe')->nullable();
            $table->integer('wertungsPunkte')->nullable();
            $table->string('wertung');

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
