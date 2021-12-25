<?php

namespace Dawnstar\Developer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Symfony\Component\Process\Process;

class DeveloperController extends Controller
{
    public function index()
    {
        return view('Developer::index');
    }

    public function command(Request $request)
    {
        $type = $request->get('type');

        if ($type) {
            Artisan::call("$type:clear");

            return response()->json(['message' => Artisan::output()], 200);
        }
        return 'ERROR';
    }

    public function env()
    {
        $breadcrumb = [
            [
                'name' => __('DeveloperLang::general.title'),
                'url' => route('dawnstar.developer.index')
            ],
            [
                'name' => __('DeveloperLang::general.box.env_edit'),
                'url' => 'javascript:void(0)'
            ]
        ];
        $env = file_get_contents(base_path('.env'));
        return view('Developer::env', compact('env', 'breadcrumb'));
    }

    public function envUpdate(Request $request)
    {
        $env = $request->get('env');

        if ($env) {
            file_put_contents(base_path('.env'), $env);
        }
        return redirect()->route('dawnstar.developer.index');
    }

    public function maintenance()
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
        $env .= "\nDAWNSTAR_MAINTENANCE=" . $value . "\n";

        file_put_contents(base_path('.env'), $env);
        return redirect()->route('dawnstar.developer.index');
    }
}
