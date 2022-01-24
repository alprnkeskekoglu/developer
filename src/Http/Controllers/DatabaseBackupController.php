<?php

namespace Dawnstar\Developer\Http\Controllers;

use Dawnstar\Developer\Http\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    /** @var DatabaseBackupService */
    private $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $databases = $this->backupService->getDatabases();

        return view('Developer::database', compact('databases'));
    }

    public function export()
    {
        $this->backupService->exportDatabase();

        return redirect()->back();
    }

    public function import(Request $request)
    {
        $this->backupService->importDatabase($request->get('file'));

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse|StreamedResponse
     */
    public function download(Request $request)
    {
        $database = $this->backupService->downloadDatabase($request->get('file'));

        if ($database) {
            return $database;
        }

        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $this->backupService->deleteDatabase($request->get('file'));

        return redirect()->back();
    }
}
