<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegelnToLiverounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_rounds', function (Blueprint $table) {
            $table->boolean('karlchenFangen')->nullable()->after('id');
            $table->boolean('karlchen')->nullable()->after('id');
            $table->boolean('koenigsSolo')->nullable()->after('id');
            $table->boolean('fuchsSticht')->nullable()->after('id');
            $table->boolean('schweinchen')->nullable()->after('id');
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
            $table->dropColumn('schweinchen');
            $table->dropColumn('fuchsSticht');
            $table->dropColumn('koenigsSolo');
            $table->dropColumn('karlchenFangen');
            $table->dropColumn('karlchen');
        });
    }
}
