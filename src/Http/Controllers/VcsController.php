<?php

namespace Dawnstar\Developer\Http\Controllers;

use Carbon\Carbon;
use CzProject\GitPhp\Git;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Symfony\Component\Process\Process;

class VcsController extends Controller
{
    private $git;

    public function __construct()
    {
        $this->git = new Git();
        $this->repo = $this->git->open(base_path());
    }

    public function index()
    {
        $breadcrumb = [
            [
                'name' => __('DeveloperLang::general.title'),
                'url' => route('dawnstar.developer.index')
            ],
            [
                'name' => __('DeveloperLang::general.box.vcs'),
                'url' => 'javascript:void(0)'
            ]
        ];

        $this->repo->fetch('origin');

        $currentBranch = $this->repo->getCurrentBranchName();
        $branches = $this->repo->getLocalBranches();
        $remoteUrl = $this->getRemoteUrl();
        $lastCommit = $this->repo->getLastCommit();
        $commits = $this->getCommits($currentBranch);

        return view('DeveloperView::vcs', compact('breadcrumb', 'currentBranch', 'branches', 'remoteUrl', 'lastCommit', 'commits'));
    }

    public function checkout(Request $request)
    {
        $newBranch = $request->get('branch');
        $this->repo->checkout($newBranch);

        return back();
    }

    public function merge(Request $request)
    {
        $commit = $request->get('commit');
        $branch = $this->repo->getCurrentBranchName();

        $this->repo->fetch('origin');
        $this->repo->merge('origin/' . $branch);

        $this->repo->execute(['reset', mb_substr($commit, 0, 8), '--hard']);

        return back();
    }

    private function getCommits($currentBranch)
    {
        $textArr = $this->repo->execute(['log', 'HEAD..origin/' . $currentBranch]);

        $commits = [];
        $i = 0;
        foreach ($textArr as $text) {
            if (strpos($text, 'commit ') !== false) {
                $i++;
                $commits[$i]['id'] = explode(' ', $text)[1];
                $commits[$i]['message'] = '';
            } elseif (strpos($text, 'Author:') !== false) {
                $regex = '/(Author: )([A-Za-zöğışçöÖÇŞİĞÜ ]+)[<]([a-zA-Z0-9.@]+)[>]/m';
                preg_match_all($regex, $text, $matches, PREG_SET_ORDER, 0);

                $commits[$i]['author']['name'] = trim(isset($matches[0][2]) ? $matches[0][2] : '');
                $commits[$i]['author']['email'] = trim(isset($matches[0][3]) ? $matches[0][3] : '');
            } elseif (strpos($text, 'Date:') !== false) {
                $commits[$i]['date'] = Carbon::parse(explode('Date: ', $text)[1]);
            } else {
                $commits[$i]['message'] .= $text;
                $commits[$i]['message'] = trim($commits[$i]['message']);
            }
        }

        return $commits;
    }

    private function getRemoteUrl()
    {
        $remote = collect($this->repo->execute(['remote', '-v']));

        $remote = trim(str_replace(['origin'], ['',], $remote->first()));

        $remote = explode(' ', $remote)[0];
        if (strpos($remote, 'git@') !== false) {
            $remote = 'https://' . str_replace(['git@', ':', '.git'], ['', '/', ''], $remote);
        }
        return $remote;
    }

}
