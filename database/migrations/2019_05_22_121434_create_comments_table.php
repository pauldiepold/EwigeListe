<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
			$table->text('body');
			$table->unsignedInteger('commentable_id')->index()->nullable();
    		$table->string('commentable_type')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();			
    		$table->softDeletes();
			
            $table->foreign('created_by')
                ->references('id')
				->on('players');
            $table->foreign('parent_id')
                ->references('id')
				->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
