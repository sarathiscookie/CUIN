<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessEntryCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_entry_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_entry_id')->unsigned();
            $table->integer('user_id');
            $table->text('content');
            $table->timestamp('created_at');

            $table->foreign('process_entry_id')->references('id')->on('process_entries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('process_entry_comments');
    }
}
