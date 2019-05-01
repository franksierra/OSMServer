<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('app_id');
            $table->enum("platform", ['android', 'ios', 'web', 'other']);
            $table->string("key", 128);
            $table->boolean('active')->default(1);
            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('apps');

            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_keys');
    }
}
