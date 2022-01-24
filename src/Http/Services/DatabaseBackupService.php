<?php

namespace Dawnstar\Developer\Http\Services;

use Dawnstar\Core\Models\Admin;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

/**
 * Class DatabaseBackupService
 * @package Dawnstar\Developer\Http\Services
 */
class DatabaseBackupService
{
    /**
     * @return array
     */
    public function getDatabases(): array
    {
        $databases = [];
        $files = Storage::allFiles('databases');

        foreach ($files as $file) {
            $splittedName = explode('_', $file);

            if (isset($splittedName[2])) {
                $user = Admin::find($splittedName[2]);
            }

            $databases[] = [
                'name' => basename($file),
                'user' => isset($user) ? $user->full_name : 'N\A',
                'size' => unitSizeForHuman(Storage::size($file)),
                'date' => date('d.m.Y, H:i:s', Storage::lastModified($file)),
                'file' => $file,
            ];
        }

        return $databases;
    }

    /**
     * @return void
     */
    public function exportDatabase(): void
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $file = storage_path('app/databases/sql_'.date("Y-m-d-H-i-s").'_'.auth('admin')->id().'_.sql');
        $views = DB::select("SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA != 'sys'");
        $viewNames = '';

        if (!is_dir(storage_path('app/databases'))) {
            mkdir(storage_path('app/databases'));
        }

        foreach ($views as $view) {
            $viewNames .= ' --ignore-table='.config('database.connections.mysql.database').'.'.$view->TABLE_NAME;
        }

        $connection = 'mysqldump -h '.config('database.connections.mysql.host').
            ' -P '.config('database.connections.mysql.port').
            ' -u '.config('database.connections.mysql.username').
            ' -p'.config('database.connections.mysql.password').
            ' '.config('database.connections.mysql.database').
            $viewNames.
            ' > '.$file;

        $process = Process::fromShellCommandline($connection);
        $process->run();
    }

    /**
     * @param string $file
     * @return void
     */
    public function importDatabase(string $file): void
    {
        if ($file) {
            set_time_limit(0);
            ini_set('max_execution_time', 0);

            $file = Storage::path($file);

            // Drop all database tables
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Artisan::call('migrate:reset', ['--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $connection = 'mysql -h '.config('database.connections.mysql.host').
                ' -P '.config('database.connections.mysql.port').
                ' -u '.config('database.connections.mysql.username').
                ' -p'.config('database.connections.mysql.password').
                ' '.config('database.connections.mysql.database').
                ' < '.$file;

            $process = Process::fromShellCommandline($connection);
            $process->run();
        }
    }

    /**
     * @param $file
     * @return StreamedResponse|null
     */
    public function downloadDatabase($file): ?StreamedResponse
    {
        if ($file) {
            return Storage::download($file);
        }

        return null;
    }

    /**
     * @param $file
     * @return void
     */
    public function deleteDatabase($file): void
    {
        if ($file) {
            Storage::delete($file);
        }
    }
}
