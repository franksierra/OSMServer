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
            $table->bigInteger("relation_id", false)->primary();
            $table->bigInteger('parent_relation_id');
            $table->string('name');
            $table->multiPolygon("geometry")->nullable();
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
