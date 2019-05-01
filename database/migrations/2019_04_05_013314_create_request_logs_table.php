<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('origin', ['web', 'api']);
            $table->unsignedBigInteger('app_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 15);
            $table->string('uri', 255);
            $table->longText('headers');
            $table->longText('params');
            $table->ipAddress('ip');
            $table->smallInteger('status_code')->nullable();
            $table->longText('response')->nullable();
            $table->float('exec_time')->nullable();

            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('apps');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_logs');
    }
}
