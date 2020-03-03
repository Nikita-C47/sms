<?php

namespace App\Providers;

use App\Components\Inherited\CyrillicResponseFactory;
use App\Models\Events\Event;
use App\Models\Events\EventAttachment;
use App\Models\Events\EventMeasure;
use App\Observers\EventAttachmentObserver;
use App\Observers\EventMeasureObserver;
use App\Observers\EventModelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Event::observe(EventModelObserver::class);
        EventMeasure::observe(EventMeasureObserver::class);
        EventAttachment::observe(EventAttachmentObserver::class);
    }
}
