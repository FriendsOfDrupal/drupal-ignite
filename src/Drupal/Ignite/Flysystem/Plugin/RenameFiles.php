<?php

namespace Drupal\Ignite\Flysystem\Plugin;

use League\Flysystem\Plugin\AbstractPlugin;

class RenameFiles extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'renameFiles';
    }

    /**
     * Rename all the files that have a placeholder in their name using the given placeholders and values.
     *
     * @param string $directory
     * @param bool   $recursive
     */
    public function handle($directory = '', $recursive = false, array $replacements = [])
    {
        $files = $this->filesystem->listFiles($directory, true);

        array_walk($files, function (array $file) use ($replacements) {
            $oldPath = '/' . $file['path'];
            $newPath = $oldPath;

            foreach ($replacements as $placeholder => $value) {
                $newPath = str_replace($placeholder, $value, $newPath);
            }

            if ($oldPath === $newPath) {
                return;
            }

            $this->filesystem->rename($oldPath, $newPath);
        });
    }
}
