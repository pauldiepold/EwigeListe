<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegeln2ToLiveRounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_rounds', function (Blueprint $table) {
            $table->boolean('schweinchenTrumpfsolo')->nullable()->after('fuchsSticht');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_rounds', function (Blueprint $table) {
            $table->dropColumn('schweinchenTrumpfsolo');
        });
    }
}
