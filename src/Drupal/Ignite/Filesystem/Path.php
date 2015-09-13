<?php

namespace Drupal\Ignite\Filesystem;

use InvalidArgumentException;

final class Path
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException(
                '$path parameter should be a string, received: ' . var_export($path, true)
            );
        }

        $this->path = $path;
    }

    public function __toString()
    {
        return $this->path;
    }
}
