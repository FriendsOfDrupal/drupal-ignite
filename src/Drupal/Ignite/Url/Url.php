<?php

namespace Drupal\Ignite\Url;

use InvalidArgumentException;

final class Url
{
    /**
     * @param string $url
     */
    public function __construct($url)
    {
        if (!is_string($url)) {
            throw new InvalidArgumentException(
                '$url parameter should be a string, received: ' . var_export($url, true)
            );
        }

        $this->url = $url;
    }

    public function __toString()
    {
        return $this->url;
    }
}
