<?php

namespace App\Providers;

use App\Components\Inherited\CyrillicResponseFactory;
use App\Models\Events\Event;
use App\Models\Events\EventAttachment;
use App\Models\Events\EventMeasure;
use App\Observers\EventAttachmentModelObserver;
use App\Observers\EventMeasureModelObserver;
use App\Observers\EventModelObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Класс, представляющий провайдер сервисов приложения.
 * @package App\Providers Провайдеры приложения.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Регистрирует сервисы приложения.
     *
     * @return void
     */
    public function register()
    {
        // Регистрируем новую фабрику для ответа, чтобы можно было отдавать файлы на кириллице
        $this->app->singleton('Illuminate\Contracts\Routing\ResponseFactory', function ($app) {
            return new CyrillicResponseFactory($app['Illuminate\Contracts\View\Factory'], $app['redirect']);
        });
    }

    /**
     * Загружает сервисы приложения..
     *
     * @return void
     */
    public function boot()
    {
        // Регистрируем наблюдатель для модели события
        Event::observe(EventModelObserver::class);
        // Регистрируем наблюдатель для модели мероприятия по событию
        EventMeasure::observe(EventMeasureModelObserver::class);
        // Регистрируем наблюдатель для модели вложения события
        EventAttachment::observe(EventAttachmentModelObserver::class);
    }
}
