<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

    public function boot(): void
    {
        // Force HTTPS scheme in production environments if behind SSL proxy
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Dynamically share company branding and settings with all blade views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $companyName = Setting::get('company_name', config('app.name', 'HRMS'));
                    $companyWebsite = Setting::get('company_website', '');
                    $companyEmail = Setting::get('company_email', '');
                    $companyPhone = Setting::get('company_phone', '');
                    $companyAddress = Setting::get('company_address', '');
                    $currencySymbol = Setting::get('currency_symbol', '₹');
                    $systemTimezone = Setting::get('timezone', config('app.timezone', 'Asia/Kolkata'));

                    // Ensure PHP and Carbon runtime timezone reflects the setting
                    if ($systemTimezone && date_default_timezone_get() !== $systemTimezone) {
                        date_default_timezone_set($systemTimezone);
                    }

                    $view->with([
                        'globalCompanyName' => $companyName,
                        'globalCompanyWebsite' => $companyWebsite,
                        'globalCompanyEmail' => $companyEmail,
                        'globalCompanyPhone' => $companyPhone,
                        'globalCompanyAddress' => $companyAddress,
                        'globalCurrencySymbol' => $currencySymbol,
                        'globalTimezone' => $systemTimezone,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore during migrations or initial bootstrap
            }
        });
    }
}
