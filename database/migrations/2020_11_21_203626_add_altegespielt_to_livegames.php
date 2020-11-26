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
            $table->integer('kontrasOffengelegt')->nullable()->after('aktuellerStich');
            $table->integer('resOffengelegt')->nullable()->after('aktuellerStich');
            $table->text('winners')->nullable()->after('wertung');
            $table->text('augen')->nullable()->after('wertung');
            $table->text('stiche')->nullable()->after('aktuellerStich');
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
            $table->dropColumn('stiche');
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
