<?php

namespace App\Providers;

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
        $this->app->bind('App\Http\Controllers\Api\v1\Interface\Person\PersonInterface','App\Http\Controllers\Api\v1\Repositories\Person\PersonRepository');
        $this->app->bind('App\Http\Controllers\Api\v1\Interface\Member\MemberInterface','App\Http\Controllers\Api\v1\Repositories\Member\MemberRepository');
        $this->app->bind('App\Http\Controllers\Api\v1\Interface\Common\CommonInterface','App\Http\Controllers\Api\v1\Repositories\Common\CommonRepository');
        $this->app->bind('App\Http\Controllers\Api\v1\Interface\Common\SmsInterface','App\Http\Controllers\Api\v1\Repositories\Common\SmsRepository');

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
