<?php

namespace Dawnstar\Developer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Dawnstar\Core\Models\Admin;
use Symfony\Component\Process\Process;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        $files = Storage::allFiles('databases');

        $databases = [];
        foreach ($files as $file) {
            $splitedName = explode('_', $file);
            if(isset($splitedName[2])) {
                $user = Admin::find($splitedName[2]);
            }

            $databases[] = [
                'name' => basename($file),
                'user' => isset($user) ? $user->full_name : 'N\A',
                'size' => unitSizeForHuman(Storage::size($file)),
                'date' => date('d.m.Y, H:i:s', Storage::lastModified($file)),
                'file' => $file
            ];
        }

        return view('Developer::database', compact('databases'));
    }

    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $file = storage_path('app/databases/sql_' . date("Y-m-d-H-i-s") . '_'. auth('admin')->id() .'_.sql');

        if (!is_dir(storage_path('app/databases'))) {
            mkdir(storage_path('app/databases'));
        }

        $views = DB::select("SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA != 'sys'");
        $viewNames = '';
        foreach ($views as $view) {
            $viewNames .= ' --ignore-table=' . config('database.connections.mysql.database') . '.' . $view->TABLE_NAME;
        }

        $connection = 'mysqldump -h ' . config('database.connections.mysql.host') .
            ' -P ' . config('database.connections.mysql.port') .
            ' -u ' . config('database.connections.mysql.username') .
            ' -p' . config('database.connections.mysql.password') .
            ' ' . config('database.connections.mysql.database') .
            $viewNames .
            ' > ' . $file;

        $process = Process::fromShellCommandline($connection);
        $process->run();

        return redirect()->back();
    }

    public function import(Request $request)
    {
        $file = $request->get('file');
        if($file) {
            set_time_limit(0);
            ini_set('max_execution_time', 0);

            $file = Storage::path($file);

            // Drop all database tables
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Artisan::call('migrate:reset', ['--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $connection = 'mysql -h ' . config('database.connections.mysql.host') .
                ' -P ' . config('database.connections.mysql.port') .
                ' -u ' . config('database.connections.mysql.username') .
                ' -p' . config('database.connections.mysql.password') .
                ' ' . config('database.connections.mysql.database') .
                ' < ' . $file;

            $process = Process::fromShellCommandline($connection);
            $process->run();
        }
        return redirect()->back();
    }

    public function download(Request $request)
    {
        $file = $request->get('file');
        if($file) {
            return Storage::download($file);
        }
        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $file = $request->get('file');
        if($file) {
            Storage::delete($file);
        }
        return redirect()->back();
    }
}
