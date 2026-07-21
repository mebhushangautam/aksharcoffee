<?php

namespace App\Traits;

use App\Helper\Reply;
use App\Support\InstallCheck;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait ModuleLicense
{
    private mixed $moduleSetting;

    private function moduleSetting(string $module): void
    {
        $settingClass = config($module . '.setting');
        $this->moduleSetting = (new $settingClass)::first();
    }

    public function isModuleLegal(string $module): bool
    {
        return true;
    }

    public function isLocalHost(string $module): bool
    {
        return true;
    }

    public function verifyModulePurchase(string $module): View
    {
        $this->modulePurchaseVerified($module, 'local-' . strtolower($module));

        return view('custom-modules.ajax.verify-success', compact('module'));
    }

    public function modulePurchaseVerified(string $module, ?string $purchaseCode = null): array
    {
        $this->saveToModuleSettings($purchaseCode, $module);

        return Reply::successWithData(
            'Module verified. <a href="">Click to go back</a>',
            ['server' => ['status' => 'success']]
        );
    }

    public function saveToModuleSettings(?string $purchaseCode, string $module): void
    {
        $this->moduleSetting($module);

        if (! $this->moduleSetting) {
            return;
        }

        $this->moduleSetting->purchase_code = $purchaseCode ?? 'local-' . strtolower($module);

        if (Schema::hasColumn($this->moduleSetting->getTable(), 'last_license_verified_at')) {
            $this->moduleSetting->last_license_verified_at = now();
        }

        $this->moduleSetting->save();
    }

    public function showInstall(): void
    {
        InstallCheck::ensureDatabaseConnected();
    }
}
