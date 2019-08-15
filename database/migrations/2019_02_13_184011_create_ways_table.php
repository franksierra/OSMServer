<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ways', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("id", false, true)->primary();
            $table->bigInteger("changeset_id", false, true);
            $table->boolean("visible");
            $table->timestamp("timestamp");
            $table->bigInteger("version", false, true);
            $table->bigInteger("uid", false, true);
            $table->string("user");
        });

        Schema::create('way_tags', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("way_id", false, true)->nullable(false);
            $table->string("k");
            $table->string("v");

            $table->index("way_id","way_id");
        });


        Schema::create('way_nodes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("way_id", false, true);
            $table->bigInteger("node_id", false, true);
            $table->bigInteger("sequence");

            $table->index("way_id","way_id");
            $table->index("node_id","node_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('way_nodes');
        Schema::dropIfExists('way_tags');
        Schema::dropIfExists('ways');
    }
}
