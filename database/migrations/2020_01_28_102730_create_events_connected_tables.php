<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsConnectedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_measures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->text('text');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('event_responsible_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('event_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('name', 16);
            $table->string('original_name');
            $table->string('extension', 10);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_measures', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('event_measures');

        Schema::table('event_responsible_departments', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('event_responsible_departments');

        Schema::table('event_attachments', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('event_attachments');

    }
}
