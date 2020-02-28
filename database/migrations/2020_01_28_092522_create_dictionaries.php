<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDictionaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Отделы
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->after('access_level')->nullable();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // Типы событий
        Schema::create('event_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        // К чему относятся события
        Schema::create('event_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        // Категории событий
        Schema::create('event_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->unsignedBigInteger('department_id');
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
        // Рейсы
        Schema::create('flights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('departure_datetime');
            $table->timestamp('arrival_datetime');
            $table->string('number', 10);
            $table->string('board', 10);
            $table->string('aircraft_code', 10)->nullable();
            $table->string('departure_airport');
            $table->string('arrival_airport');
            $table->string('captain');
            $table->string('extra_captain')->nullable();
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
        Schema::dropIfExists('flights');

        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::dropIfExists('event_categories');
        Schema::dropIfExists('event_relations');
        Schema::dropIfExists('event_types');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::dropIfExists('departments');
    }
}
