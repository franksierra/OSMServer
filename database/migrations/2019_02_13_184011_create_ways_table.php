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
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('changeset_id');
            $table->boolean('visible');
            $table->timestamp('timestamp');
            $table->unsignedInteger('version');
            $table->unsignedBigInteger('uid');
            $table->string('user');
        });

        Schema::create('way_tags', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->unsignedBigInteger('way_id')->nullable(false);
            $table->string('k', 191);
            $table->string('v');

            $table->index('way_id', 'way_id');
            $table->index('k', 'k');
            $table->unique(['way_id', 'k'], 'unique_way_id_k');
        });


        Schema::create('way_nodes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->unsignedBigInteger('way_id');
            $table->unsignedBigInteger('node_id');
            $table->unsignedInteger('sequence');

            $table->index('way_id', 'way_id');
            $table->index('node_id', 'node_id');
            $table->unique(['way_id', 'node_id', 'sequence'], 'way_nodes_uniqueness');
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
