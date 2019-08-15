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
            $table->engine = 'MyISAM';
            $table->bigInteger("id", false, true)->primary();
            $table->decimal("latitude", 13, 10);
            $table->decimal("longitude", 13, 10);
            $table->bigInteger("changeset_id", false, true);
            $table->boolean("visible");
            $table->timestamp("timestamp");
            $table->bigInteger("version", false, true);
            $table->bigInteger("uid", false, true);
            $table->string("user");
        });

        Schema::create('node_tags', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("node_id", false, true)->nullable(false);
            $table->string("k");
            $table->string("v");

            $table->index("node_id", "node_id");
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
