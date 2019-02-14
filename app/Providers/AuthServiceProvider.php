<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Jurisdiction::class => \App\Policies\JurisdictionPolicy::class,
        \App\Models\InternationalTelephoneCode::class => \App\Policies\InternationalTelephoneCodePolicy::class,
        \App\Models\Talk::class => \App\Policies\TalkPolicy::class,
        \App\Models\ForumNode::class => \App\Policies\ForumNodePolicy::class,
        \App\Models\ForumThread::class => \App\Policies\ForumThreadPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
