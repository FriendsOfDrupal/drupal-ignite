<?php

namespace Drupal\Ignite\Flysystem\Plugin;

use League\Flysystem\Plugin\AbstractPlugin;

class ListDirs extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'listDirs';
    }

    /**
     * List all directories in the directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function handle($directory = '', $recursive = false)
    {
        $contents = $this->filesystem->listContents($directory, $recursive);

        $filter = function ($object) {
            return $object['type'] === 'dir';
        };

        return array_values(array_filter($contents, $filter));
    }
}
