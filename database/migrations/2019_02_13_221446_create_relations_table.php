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
            $table->engine = 'MyISAM';
            $table->bigInteger("id", false, true)->primary();
            $table->bigInteger("changeset_id", false, true);
            $table->boolean("visible");
            $table->timestamp("timestamp");
            $table->bigInteger("version", false, true);
            $table->bigInteger("uid", false, true);
            $table->string("user");
        });

        Schema::create('relation_tags', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("relation_id", false, true)->nullable(false);
            $table->string("k", 191);
            $table->string("v");

            $table->index("relation_id","relation_id");
            $table->unique(['relation_id', 'k'], 'unique_relation_id_k');
        });

        Schema::create('relation_members', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger("relation_id", false, true);
            $table->enum("member_type", ["node", "way", "relation"]);
            $table->bigInteger("member_id", false, true);
            $table->string("member_role");
            $table->bigInteger("sequence");

            $table->index("relation_id","relation_id");
            $table->index("member_id","member_id");
            $table->unique(['relation_id', 'member_id'], 'unique_relation_id_member_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_members');
        Schema::dropIfExists('relation_tags');
        Schema::dropIfExists('relations');
    }
}
