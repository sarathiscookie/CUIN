<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('process_entry_id')->unsigned();
            $table->string('title', 100);
            $table->text('description');
            $table->timestamps();

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
        Schema::drop('process_files');
    }
}
