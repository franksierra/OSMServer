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
            $table->unsignedBigInteger('id')->primary();
            $table->decimal('latitude', 13, 10);
            $table->decimal('longitude', 13, 10);
            $table->unsignedBigInteger('changeset_id');
            $table->boolean('visible');
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('version');
            $table->unsignedBigInteger('uid');
            $table->string('user');
        });

        Schema::create('node_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('node_id')->nullable(false);
            $table->string('k');
            $table->string('v');

            $table->unique(['node_id', 'k'], 'node_tags-unique_node_id_k');
            $table->index('node_id', 'node_tags-node_id');
            $table->index('k', 'node_tags-k');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_tags');
        Schema::dropIfExists('nodes');
    }
}
