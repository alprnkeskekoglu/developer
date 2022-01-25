<?php

namespace Dawnstar\Developer\Http\Controllers;

use CzProject\GitPhp\GitException;
use Dawnstar\Developer\Http\Services\BreadcrumbService;
use Dawnstar\Developer\Http\Services\VcsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class VcsController
 * @package Dawnstar\Developer\Http\Controllers
 */
class VcsController extends Controller
{
    /** @var VcsService */
    private $vcsService;
    /** @var BreadcrumbService */
    private $breadcrumbService;

    /**
     * @param VcsService $vcsService
     * @param BreadcrumbService $breadcrumbService
     */
    public function __construct(VcsService $vcsService, BreadcrumbService $breadcrumbService)
    {
        $this->vcsService = $vcsService;
        $this->breadcrumbService = $breadcrumbService;
    }

    /**
     * @throws GitException
     */
    public function index()
    {
        $breadcrumb = $this->breadcrumbService->getVcsBreadcrumb();
        $info = $this->vcsService->getRepositoryCurrentBranchInfo();

        return view('DeveloperView::vcs', array_merge_recursive($breadcrumb, $info));
    }

    /**
     * @throws GitException
     * @return RedirectResponse
     */
    public function checkout(Request $request)
    {
        $this->vcsService->checkout($request->get('branch'));

        return back();
    }

    /**
     * @param Request $request
     * @throws GitException
     * @return RedirectResponse
     */
    public function merge(Request $request)
    {
        $this->vcsService->merge($request->get('commit'));

        return back();
    }
}
