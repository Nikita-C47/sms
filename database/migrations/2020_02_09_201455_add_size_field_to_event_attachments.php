<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Класс, представляющий миграцию добавления поля размера в таблицу вложений.
 */
class AddSizeFieldToEventAttachments extends Migration
{
    /**
     * Запускает миграцию.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_attachments', function (Blueprint $table) {
            $table->bigInteger('size');
        });
    }

    /**
     * Откатывает миграцию.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_attachments', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
}
