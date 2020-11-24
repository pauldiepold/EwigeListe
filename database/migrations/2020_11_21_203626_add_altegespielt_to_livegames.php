<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltegespieltToLivegames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_games', function (Blueprint $table) {
            $table->integer('alte_gespielt')->nullable()->after('dran');
            $table->boolean('geheiratet')->nullable()->after('dran');
            $table->text('messages')->nullable()->after('dran');
            $table->boolean('geschmissen')->nullable()->after('dran');
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
            $table->dropColumn('alte_gespielt');
            $table->dropColumn('geheiratet');
            $table->dropColumn('messages');
            $table->dropColumn('geschmissen');
        });
    }
}
