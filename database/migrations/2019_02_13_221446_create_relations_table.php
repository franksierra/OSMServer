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
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('changeset_id');
            $table->boolean('visible');
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('version');
            $table->unsignedBigInteger('uid');
            $table->string('user');
        });

        Schema::create('relation_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('relation_id')->nullable(false);
            $table->string('k');
            $table->string('v');

            $table->unique(['relation_id', 'k'], 'relation_tags-unique_relation_id_k');
            $table->index('relation_id', 'relation_tags-relation_id');
        });

        Schema::create('relation_members', function (Blueprint $table) {
            $table->unsignedBigInteger('relation_id');
            $table->enum('member_type', ['node', 'way', 'relation']);
            $table->unsignedBigInteger('member_id');
            $table->string('member_role');
            $table->unsignedInteger('sequence');

            $table->unique(
                ['relation_id', 'member_type', 'member_id', 'member_role', 'sequence'],
                'relation_members-uniqueness'
            );
            $table->index('relation_id', 'relation_members-relation_id');
            $table->index('member_id', 'relation_members-member_id');
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
