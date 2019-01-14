<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('db:create:founder', function () {
    $countries = \App\Models\InternationalTelephoneCode::get()->pluck('name', 'code');
    $ttc = $this->choice('Select country', $countries->all());
    $phone = $this->ask('Enter your mobile number');
    $user = new \App\Models\User([
        'international_telephone_code' => $ttc,
        'phone' => $phone,
    ]);
    $user->save();
    \App\Models\Jurisdiction::nodes()->each(function (string $node) use ($user) {
        $user->jurisdictions()->create(['node' => $node]);
    });
    $this->comment(sprintf('Created a User:%s success.', $user->id));
})->describe('Create a founder');
