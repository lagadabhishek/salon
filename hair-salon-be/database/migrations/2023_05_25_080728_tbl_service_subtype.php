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
        Schema::create('tbl_service_subtype', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedBigInteger('cat_id');
            $table->unsignedBigInteger('service_id');
            $table->string('name', 255)->nullable(false);
            $table->text('discription')->nullable();
            $table->float('price')->nullable(false);
            $table->string('time')->nullable(false);
            $table->enum('status', ['0', '1'])->nullable(false)->default('1');    
            $table->string('gender', 155)->nullable(false);
            $table->string('created_on', 155)->nullable(false);
            $table->integer('created_by', false, true)->nullable(false);
            $table->string('modified_on', 155)->nullable();
            $table->integer('modified_by', false, true)->nullable();
            $table->string('deleted_on', 155)->nullable();
            $table->integer('deleted_by', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_service_subtype');
    }
};
