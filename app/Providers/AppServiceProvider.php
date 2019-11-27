<?php

namespace App\Providers;

use App\ModelMorphMap;
use App\Models\Comment;
use App\Notifications\Channels\SmsChannel as SmsNotificationChannel;
use App\Observers\CommentObserver;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ModelMorphMap::register();
        Resource::withoutWrapping();
        Comment::observe(CommentObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EasySms::class, function () {
            return new EasySms(config('sms'));
        });
        Notification::extend('sms', function ($app) {
            return new SmsNotificationChannel(
                $app->make(EasySms::class)
            );
        });
    }
}
