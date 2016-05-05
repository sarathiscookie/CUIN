<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('hash', 100);
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('password', 100);
            $table->text('notice_internal');
            $table->text('notice_external');
            $table->integer('reference_id');
            $table->string('salutation', 10);
            $table->enum('active',['yes','no'])->default('no');
            $table->string('status', 20);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customers');
    }
}
