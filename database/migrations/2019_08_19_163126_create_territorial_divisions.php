<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerritorialDivisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('territorial_divisions', function (Blueprint $table) {
            $table->unsignedBigInteger('relation_id')->primary();
            $table->unsignedBigInteger('parent_relation_id');
            $table->string('name');
            $table->integer('admin_level');
            $table->geometry('geometry');

            $table->spatialIndex('geometry');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('territorial_divisions');
    }
}
