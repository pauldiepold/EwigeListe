<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchweinchenToLiveGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_games', function (Blueprint $table) {
            $table->boolean('schweinchen')->nullable()->after('kontrasOffengelegt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_games', function (Blueprint $table) {
            $table->dropColumn('schweinchen');
        });
    }
}
