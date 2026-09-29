<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        // 1. Super-access untuk administrator (kecuali aksi approve yang butuh paraf hierarkis)
        Gate::before(function ($user, string $ability) {
            if ($user->hasRole('administrator') && ! str_starts_with($ability, 'approve')) {
                return true;
            }
        });

        // 2. Daftarkan Policy untuk Spatie Role
        Gate::policy(Role::class, RolePolicy::class);

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
