<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_forgot_password', function(Blueprint $table){
            $table->increments('id');
            $table->integer('usr_id', false, true)->length(11);
            $table->string('email', 155)->nullable(false);
            $table->string('token', 155)->nullable(false);
            $table->string('date_created', 45)->nullable();    
            $table->enum('is_link_used', ['1', '2'])->nullable(false)->default('1');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_forgot_password');
    }
};
