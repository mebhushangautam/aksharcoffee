<?php

namespace App\Providers;

use App\Http\Controllers\Overrides\PurchaseVerificationController;
use App\Models\GlobalSetting;
use Froiden\Envato\Controllers\PurchaseVerificationController as EnvatoPurchaseVerificationController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class EnvatoLicenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EnvatoPurchaseVerificationController::class,
            PurchaseVerificationController::class
        );
    }

    public function boot(): void
    {
        $this->app->booted(function () {
            $this->ensureGlobalPurchaseCode();
        });
    }

    private function ensureGlobalPurchaseCode(): void
    {
        try {
            if (! Schema::hasTable('global_settings')) {
                return;
            }

            $setting = GlobalSetting::first();

            if (! $setting || filled($setting->purchase_code)) {
                return;
            }

            $setting->purchase_code = 'local';

            if (Schema::hasColumn($setting->getTable(), 'last_license_verified_at')) {
                $setting->last_license_verified_at = now();
            }

            $setting->save();
        } catch (\Throwable) {
            // Database may not be ready during install.
        }
    }
}
