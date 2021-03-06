<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('npcs', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->integer('classification_id');
            $table->integer('game_id');
            $table->text('name');
            $table->integer('base_health');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('npcs');
    }
}
