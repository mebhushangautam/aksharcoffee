<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class InstallCheck
{
    public static function ensureDatabaseConnected(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            echo view('vendor.froiden-envato.install_message');
            exit(1);
        }
    }
}
