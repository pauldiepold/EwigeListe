<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOldIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function(Blueprint $table)
        {
            $table->dropColumn('old_id');
        });

        Schema::table('rounds', function(Blueprint $table)
        {
            $table->dropColumn('old_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function(Blueprint $table)
        {
            $table->unsignedInteger('old_id');
        });

        Schema::table('rounds', function(Blueprint $table)
        {
            $table->unsignedInteger('old_id');
        });
    }
}
