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
            $table->bigIncrements('id');
            $table->string('replication_url')->unique();
            $table->unsignedBigInteger('replication_sequence');
            $table->unsignedBigInteger('replication_timestamp');

            $table->decimal('bbox_left', 13, 10);
            $table->decimal('bbox_bottom', 13, 10);
            $table->decimal('bbox_right', 13, 10);
            $table->decimal('bbox_top', 13, 10);


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
