<?php

namespace App\Providers;

use App\ModelMorphMap;
use App\Models\Comment;
use Overtrue\EasySms\EasySms;
use App\Observers\CommentObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\SmsChannel as SmsNotificationChannel;

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
