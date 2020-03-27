<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveRoundToRounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->unsignedInteger('live_round_id')->nullable()->after('id');

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
        Schema::table('rounds', function (Blueprint $table) {
            $table->dropForeign('rounds_live_round_id_foreign');
            $table->dropColumn('live_round_id');
        });
    }
}
