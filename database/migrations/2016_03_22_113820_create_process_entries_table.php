<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_id')->unsigned();
            $table->string('title', 100);
            $table->text('description');
            $table->enum('confirmation',['yes','no'])->default('no');
            $table->enum('comments_open',['yes','no'])->default('no');
            $table->string('status', 20);
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('process_entries');
    }
}
