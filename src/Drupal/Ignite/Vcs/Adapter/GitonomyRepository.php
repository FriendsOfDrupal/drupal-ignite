<?php

namespace Drupal\Ignite\Vcs\Adapter;

use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Url\Url;
use Drupal\Ignite\Vcs\Repository;

use Gitonomy\Git\Admin as GitAdmin;

final class GitonomyRepository implements Repository
{
    /**
     * Dowloads a local copy from a repository.
     *
     * @param Path $path the local destination of the repository
     * @param Url  $url  the remote location of the repository
     */
    public function download(Path $path, Url $url)
    {
        GitAdmin::cloneTo((string) $path, (string) $url, false);
    }
}
