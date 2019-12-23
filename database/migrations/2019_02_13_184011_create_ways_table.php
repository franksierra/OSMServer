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
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('changeset_id');
            $table->boolean('visible');
            $table->timestamp('timestamp');
            $table->unsignedInteger('version');
            $table->unsignedBigInteger('uid');
            $table->string('user');
        });

        Schema::create('way_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('way_id')->nullable(false);
            $table->string('k');
            $table->string('v');

            $table->unique(['way_id', 'k'], 'way_tags-unique_way_id_k');
            $table->index('way_id', 'way_tags-way_id');
            $table->index('k', 'way_tags-k');
        });


        Schema::create('way_nodes', function (Blueprint $table) {
            $table->unsignedBigInteger('way_id');
            $table->unsignedBigInteger('node_id');
            $table->unsignedInteger('sequence');

            $table->unique(['way_id', 'node_id', 'sequence'], 'way_nodes-way_nodes_uniqueness');
            $table->index('way_id', 'way_nodes-way_id');
            $table->index('node_id', 'way_nodes-node_id');
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
