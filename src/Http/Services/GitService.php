<?php

namespace Dawnstar\Developer\Http\Services;

use Carbon\Carbon;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitRepository;

class GitService
{
    /** @var Git */
    private $git;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    /**
     * @param $path
     * @return GitRepository
     */
    public function getRepository($path): GitRepository
    {
        return $this->git->open($path);
    }

    public function getCommits(array $logs): array
    {
        $commits = [];
        $i = 0;
        foreach ($logs as $text) {
            if (strpos($text, 'commit ') !== false) {
                $i++;
                $commits[$i]['id'] = explode(' ', $text)[1];
                $commits[$i]['message'] = '';
            } elseif (strpos($text, 'Author:') !== false) {
                $regex = '/(Author: )([A-Za-zöğışçöÖÇŞİĞÜ ]+)[<]([a-zA-Z0-9.@]+)[>]/m';
                preg_match_all($regex, $text, $matches, PREG_SET_ORDER, 0);

                $commits[$i]['author']['name'] = trim($matches[0][2] ?? '');
                $commits[$i]['author']['email'] = trim($matches[0][3] ?? '');
            } elseif (strpos($text, 'Date:') !== false) {
                $commits[$i]['date'] = Carbon::parse(explode('Date: ', $text)[1]);
            } else {
                $commits[$i]['message'] .= $text;
                $commits[$i]['message'] = trim($commits[$i]['message']);
            }
        }

        return $commits;
    }
}
