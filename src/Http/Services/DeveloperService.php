<?php

namespace Dawnstar\Developer\Http\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

/**
 * Class DeveloperService
 * @package Dawnstar\Developer\Http\Services
 */
class DeveloperService
{
    /**
     * @return string
     */
    public function getEnv(): string
    {
        return file_get_contents(base_path('.env'));
    }

    /**
     * @param string $env
     * @return void
     */
    public function updateEnv(string $env): void
    {
        if ($env) {
            file_put_contents(base_path('.env'), $env);
        }
    }

    /**
     * @param string $type
     * @return JsonResponse|string
     */
    public function executeClearCommand(string $type)
    {
        if ($type) {
            Artisan::call("$type:clear");

            return response()->json(['message' => Artisan::output()], 200);
        }

        return 'ERROR';
    }

    /**
     * @return void
     */
    public function setMaintenanceMode(): void
    {
        $value = env('DAWNSTAR_MAINTENANCE', false) == true ? 'false' : 'true';

        $env = file_get_contents(base_path('.env'));

        $env = str_replace([
            "\nDAWNSTAR_MAINTENANCE=false\n",
            "\nDAWNSTAR_MAINTENANCE=false",
            "DAWNSTAR_MAINTENANCE=false\n",
            "DAWNSTAR_MAINTENANCE=false",
            "\nDAWNSTAR_MAINTENANCE=true\n",
            "\nDAWNSTAR_MAINTENANCE=true",
            "DAWNSTAR_MAINTENANCE=true\n",
            "DAWNSTAR_MAINTENANCE=true",
        ], '', $env);
        $env .= "\nDAWNSTAR_MAINTENANCE=".$value."\n";

        file_put_contents(base_path('.env'), $env);
    }
}
