<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', function (Blueprint $table) {
            $table->bigInteger("id", false, true)->primary();
            $table->bigInteger("changeset_id", false, true);
            $table->boolean("visible");
            $table->timestamp("timestamp");
            $table->bigInteger("version", false, true);
            $table->string("user", 255);
        });
        Schema::create('relation_tags', function (Blueprint $table) {
            $table->bigInteger("relation_id", false, true)->nullable(false);
            $table->string("k", 255);
            $table->string("v", 255);

            $table->foreign('relation_id')->references('id')->on('relations');
        });

        Schema::create('relation_members', function (Blueprint $table) {
            $table->bigInteger("relation_id", false, true);
            $table->enum("member_type", ["node", "way", "relation"]);
            $table->bigInteger("member_id", false, true);
            $table->string("member_role", 255);
            $table->bigInteger("sequence");

            $table->foreign('relation_id')->references('id')->on('relations');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relation_members', function (Blueprint $table) {
            $table->dropForeign(['relation_id']);
        });
        Schema::dropIfExists('relation_members');

        Schema::table('relation_tags', function (Blueprint $table) {
            $table->dropForeign(['relation_id']);
        });
        Schema::dropIfExists('relation_tags');

        Schema::dropIfExists('relations');
    }
}
