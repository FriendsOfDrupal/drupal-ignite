<?php

namespace Drupal\Ignite\Flysystem\Plugin;

use League\Flysystem\Plugin\AbstractPlugin;

class ReplaceFileContent extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'replaceFileContent';
    }

    /**
     * Replace all the occurencies of the placeholders in the file contents
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function handle($directory = '', $recursive = false, array $replacements = [])
    {
        $files = $this->filesystem->listFiles($directory, true);

        array_walk($files, function (array $files) use ($replacements) {
            $filePath = '/' . $files['path'];

            $oldFileContent = $this->filesystem->read($filePath);
            $newFileContent = $oldFileContent;

            foreach ($replacements as $placeholder => $value) {
                $newFileContent = str_replace($placeholder, $value, $newFileContent);
            }

            $this->filesystem->put($filePath, $newFileContent);
        });
    }
}
