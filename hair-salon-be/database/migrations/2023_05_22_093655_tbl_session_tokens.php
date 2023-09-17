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
         Schema::create('tbl_session_tokens', function(Blueprint $table){
            $table->increments('id');
            $table->integer('usr_id', false, true)->length(11);
            $table->string('session_token', 155)->nullable();
            $table->enum('usr_role_id', ['1', '2', '3'])->default('1')->nullable(false);
            $table->string('usr_first_name', 55)->nullable();
            $table->string('usr_last_name', 55)->nullable();
            $table->string('usr_email', 155)->nullable(false);
            $table->string('usr_phone_number', 12)->nullable(false);    
            $table->string('created_on', 45)->nullable(false);
            $table->string('modified_on', 45)->nullable();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_session_tokens');
    }
};
