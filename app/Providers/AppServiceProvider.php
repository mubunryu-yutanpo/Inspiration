<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 商用環境以外だった場合、SQLログを出力させる
        if(config('app.env') !== 'production'){
            \DB::listen(function ($query) {
                \Log::info("Query Time:{$query->time}s] $query->sql");
            });
        }

        // メール認証用
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new VerifyEmailJapanese($url))->to($notifiable);
        });
    }
}
