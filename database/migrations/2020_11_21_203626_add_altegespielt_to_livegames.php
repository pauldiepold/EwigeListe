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
            $table->boolean('geheiratet')->nullable()->after('aktuellerStich');
            $table->text('messages')->nullable()->after('aktuellerStich');
            $table->boolean('geschmissen')->nullable()->after('aktuellerStich');
            $table->smallInteger('kontrasOffengelegt')->nullable()->after('aktuellerStich');
            $table->smallInteger('resOffengelegt')->nullable()->after('aktuellerStich');
            $table->smallInteger('kontraAbsage')->nullable()->after('aktuellerStich');
            $table->smallInteger('reAbsage')->nullable()->after('aktuellerStich');
            $table->boolean('kontraAnsage')->nullable()->after('aktuellerStich');
            $table->boolean('reAnsage')->nullable()->after('aktuellerStich');
            $table->text('winners')->nullable()->after('wertung');
            $table->text('stiche')->nullable()->after('wertung');
            $table->smallInteger('reAugen')->nullable()->after('wertung');
            $table->smallInteger('kontraAugen')->nullable()->after('wertung');
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
            $table->dropColumn('geheiratet');
            $table->dropColumn('messages');
            $table->dropColumn('geschmissen');
            $table->dropColumn('kontrasOffengelegt');
            $table->dropColumn('resOffengelegt');
            $table->dropColumn('kontraAbsage');
            $table->dropColumn('reAbsage');
            $table->dropColumn('kontraAnsage');
            $table->dropColumn('reAnsage');
            $table->dropColumn('winners');
            $table->dropColumn('stiche');
            $table->dropColumn('reAugen');
            $table->dropColumn('kontraAugen');
        });
    }
}
