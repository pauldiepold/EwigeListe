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
            $table->integer('resOffengelegt')->nullable()->after('dran');
            $table->integer('kontrasOffengelegt')->nullable()->after('dran');
            $table->boolean('geheiratet')->nullable()->after('dran');
            $table->text('messages')->nullable()->after('dran');
            $table->boolean('geschmissen')->nullable()->after('dran');
            $table->text('winners')->nullable()->after('dran');
            $table->text('augen')->nullable()->after('dran');
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
            $table->dropColumn('kontrasOffengelegt');
            $table->dropColumn('resOffengelegt');
            $table->dropColumn('geheiratet');
            $table->dropColumn('messages');
            $table->dropColumn('geschmissen');
            $table->dropColumn('winners');
            $table->dropColumn('augen');
        });
    }
}
