<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsmImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osm_imports', function (Blueprint $table) {
            $table->bigInteger("id", true);
            $table->string("country", 2);

            $table->decimal("bbox_left", 13, 10);
            $table->decimal("bbox_bottom", 13, 10);
            $table->decimal("bbox_right", 13, 10);
            $table->decimal("bbox_top", 13, 10);

            $table->bigInteger("replication_timestamp");
            $table->integer("replication_sequence", false, true);
            $table->string("replication_url");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('osm_imports');
    }
}
