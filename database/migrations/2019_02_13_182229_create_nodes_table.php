<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->bigInteger("id", false, true)->primary();
            $table->decimal("latitude", 13, 10);
            $table->decimal("longitude", 13, 10);
            $table->bigInteger("changeset_id", false, true);
            $table->boolean("visible");
            $table->timestamp("timestamp");
            $table->bigInteger("version", false, true);
            $table->string("user", 255);
        });

        Schema::create('node_tags', function (Blueprint $table) {
            $table->bigInteger("node_id", false, true)->nullable(false);
            $table->string("k", 255);
            $table->string("v", 255);
            $table->foreign('node_id')->references('id')->on('nodes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('node_tags', function (Blueprint $table) {
            $table->dropForeign(['node_id']);
        });
        Schema::dropIfExists('node_tags');
        Schema::dropIfExists('nodes');
    }
}
