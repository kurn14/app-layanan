<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id', 'en', 'ar'])
                ->labels([
                    'id' => 'Indonesia',
                    'en' => 'English',
                    'ar' => 'العربية',
                ])
                ->flags(fn () => [
                    'id' => asset('flags/id.svg'),
                    'en' => asset('flags/en.svg'),
                    'ar' => asset('flags/ar.svg'),
                ])
                ->circular()
                ->visible(outsidePanels: true);
        });
    }
}
