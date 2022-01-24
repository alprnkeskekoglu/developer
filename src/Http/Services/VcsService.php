<?php

namespace Dawnstar\Developer\Http\Services;

use CzProject\GitPhp\GitException;
use CzProject\GitPhp\GitRepository;

/**
 * Class VcsService
 * @package App\Http\Controllers\Services
 */
class VcsService
{
    /** @var GitService */
    private $vcsService;
    /** @var GitRepository */
    private $repository;

    /**
     * @param GitService $gitService
     */
    public function __construct(GitService $gitService)
    {
        $this->vcsService = $gitService;
        $this->repository = $this->vcsService->getRepository(base_path());
    }

    /**
     * @throws GitException
     * @return array[]
     */
    public function getRepositoryCurrentBranchInfo(): array
    {
        $this->repository->fetch('origin');

        $currentBranch = $this->repository->getCurrentBranchName();
        $branches = $this->repository->getLocalBranches();
        $remoteUrl = $this->getRemoteUrl();
        $lastCommit = $this->repository->getLastCommit();
        $commits = $this->getCommits($currentBranch);

        return compact('currentBranch', 'branches', 'remoteUrl', 'lastCommit', 'commits');
    }

    /**
     * @throws GitException
     * @return string
     */
    public function getRemoteUrl(): string
    {
        $remote = collect($this->repository->execute(['remote', '-v']));

        $remote = trim(str_replace(['origin'], ['',], $remote->first()));

        $remote = explode(' ', $remote)[0];

        if (strpos($remote, 'git@') !== false) {
            $remote = 'https://'.str_replace(['git@', ':', '.git'], ['', '/', ''], $remote);
        }

        return $remote;
    }

    /**
     * @param string $currentBranch
     * @throws GitException
     * @return array
     */
    private function getCommits(string $currentBranch): array
    {
        $logs = $this->repository->execute(['log', 'HEAD..origin/'.$currentBranch]);

        return $this->vcsService->getCommits($logs);
    }

    /**
     * @param string $branchName
     * @throws GitException
     * @return void
     */
    public function checkout(string $branchName): void
    {
        $this->repository->checkout($branchName);
    }

    /**
     * @param string $commit
     * @throws GitException
     * @return void
     */
    public function merge(string $commit): void
    {
        $branch = $this->repository->getCurrentBranchName();

        $this->repository->fetch('origin');
        $this->repository->merge('origin/'.$branch);

        $this->repository->execute(['reset', mb_substr($commit, 0, 8), '--hard']);
    }
}
