<?php

namespace Drupal\Ignite\Vcs\Adapter;

use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Url\Url;
use Drupal\Ignite\Vcs\Repository;
use Drupal\Ignite\Vcs\Branch\Name as BranchName;

use Gitonomy\Git\Admin as GitAdmin;

final class GitonomyRepository implements Repository
{
    /**
     * Dowloads a local copy from a repository.
     *
     * @param Path $path the local destination of the repository
     * @param Url  $url  the remote location of the repository
     * @param BranchName $branchName the name of the branch to download
     */
    public function download(Path $path, Url $url, BranchName $branchName)
    {
        GitAdmin::cloneTo((string) $path, (string) $url, false);
    }
}
