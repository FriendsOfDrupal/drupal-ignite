<?php

namespace Drupal\Ignite\Vcs;

use Drupal\Ignite\Filesystem\Path;
use Drupal\Ignite\Url\Url;

interface Repository
{
    /**
     * Dowloads a local copy from a repository.
     *
     * @param Path $path the local destination of the repository
     * @param Url  $url  the remote location of the repository
     */
    public function download(Path $path, Url $url);
}
