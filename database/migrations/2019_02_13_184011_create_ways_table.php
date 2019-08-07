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
            $table->string("k", 191);
            $table->string("v");

            $table->unique(['way_id', 'k']);
        });


        Schema::create('way_nodes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("way_id", false, true);
            $table->bigInteger("node_id", false, true);
            $table->bigInteger("sequence");

            $table->unique(['way_id', 'node_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('way_nodes', function (Blueprint $table) {
            $table->dropForeign(['way_id']);
            $table->dropForeign(['node_id']);
        });
        Schema::dropIfExists('way_nodes');
        Schema::table('way_tags', function (Blueprint $table) {
            $table->dropForeign(['way_id']);
        });
        Schema::dropIfExists('way_tags');
        Schema::dropIfExists('ways');
    }
}
