<?php

namespace Dawnstar\Developer\Http\Controllers;

use Dawnstar\Developer\Http\Services\BreadcrumbService;
use Dawnstar\Developer\Http\Services\DeveloperService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class DeveloperController
 * @package Dawnstar\Developer\Http\Controllers
 */
class DeveloperController extends Controller
{
    /** @var BreadcrumbService */
    private $breadcrumbService;
    /** @var DeveloperService */
    private $developerService;

    /**
     * @param BreadcrumbService $breadcrumbService
     * @param DeveloperService $developerService
     */
    public function __construct(BreadcrumbService $breadcrumbService, DeveloperService $developerService)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->developerService = $developerService;
    }

    /**
     * @return View
     */
    public function index()
    {
        return view('Developer::index');
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     */
    public function command(Request $request)
    {
        return $this->developerService->executeClearCommand($request->get('type'));
    }

    /**
     * @return View
     */
    public function env()
    {
        $breadcrumb = $this->breadcrumbService->getEnvBreadcrumb();
        $env = $this->developerService->getEnv();

        return view('Developer::env', compact('env', 'breadcrumb'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateEnv(Request $request)
    {
        $this->developerService->updateEnv($request->get('env'));

        return redirect()->route('dawnstar.developer.index');
    }

    /**
     * @return RedirectResponse
     */
    public function maintenance()
    {
        $this->developerService->setMaintenanceMode();

        return redirect()->route('dawnstar.developer.index');
    }
}
