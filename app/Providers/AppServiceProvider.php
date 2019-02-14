<?php

namespace App\Providers;

use App\ModelMorphMap;
use App\Models\Comment;
use Overtrue\EasySms\EasySms;
use App\Observers\CommentObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;

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
    }
}
