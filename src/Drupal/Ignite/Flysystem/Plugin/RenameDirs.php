<?php

namespace Drupal\Ignite\Flysystem\Plugin;

use League\Flysystem\Plugin\AbstractPlugin;

class RenameDirs extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'renameDirs';
    }

    /**
     * Rename all directories in the directory using the given placeholders and values.
     *
     * Process all the folders that have a placeholder in their name,
     * checking they still exist after having moved the files.
     *
     * @param string $directory
     * @param bool   $recursive
     */
    public function handle($directory = '', $recursive = false, array $replacements = [])
    {
        $dirs = $this->filesystem->listDirs($directory, true);

        array_walk($dirs, function (array $dir) use ($replacements) {
            $oldPath = '/' . $dir['path'];
            $newPath = $oldPath;

            foreach ($replacements as $placeholder => $value) {
                $newPath = str_replace($placeholder, $value, $newPath);
            }

            if ($oldPath === $newPath) {
                return;
            }

            if ($this->filesystem->has($oldPath)) {
                return;
            }

            $this->filesystem->rename($oldPath, $newPath);
        });

    }
}
