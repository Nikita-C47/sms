<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Класс, представляюший миграцию создания таблицы событий.
 */
class CreateEventsTable extends Migration
{
    /**
     * Запускает миграцию.
     *
     * @return void
     */
    public function up()
    {
        // События
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->unsignedBigInteger('flight_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('relation_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->boolean('approved')->nullable();
            $table->string('initiator')->nullable();
            $table->string('airport')->nullable();
            $table->string('status', 15)->default('new');
            $table->text('message');
            $table->text('commentary')->nullable();
            $table->text('reason')->nullable();
            $table->text('decision')->nullable();
            $table->timestamp('fix_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('relation_id')->references('id')->on('event_relations')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('event_types')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('event_categories')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Откатывает миграцию.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['type_id']);
            $table->dropForeign(['relation_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['flight_id']);
        });

        Schema::dropIfExists('events');
    }
}
