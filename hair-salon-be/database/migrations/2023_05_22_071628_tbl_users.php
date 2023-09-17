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
        Schema::create('tbl_users', function(Blueprint $table){
            $table->increments('usr_id');
            $table->enum('usr_role_id', ['1', '2', '3'])->default('1')->nullable(false);
            $table->string('usr_first_name', 55)->nullable();
            $table->string('usr_last_name', 55)->nullable();
            $table->string('usr_email', 155)->nullable(false);
            $table->string('usr_phone_number', 12)->nullable(false);    
            $table->string('usr_password', 155)->nullable(false);
            $table->enum('usr_gender', ['MALE', 'FEMALE', 'OTHER'])->nullable(false);
            $table->string('usr_last_login', 45)->nullable(false);
            $table->string('usr_last_ip', 45)->nullable(false);
            $table->string('usr_created_on', 45)->nullable(false);
            $table->string('usr_modified_on', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_users');
    }
};
