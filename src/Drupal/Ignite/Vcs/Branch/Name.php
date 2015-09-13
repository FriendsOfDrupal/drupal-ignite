<?php

namespace Drupal\Ignite\Vcs\Branch;

use InvalidArgumentException;

final class Name
{
    /**
     * @var string
     */
    private $branchName;

    /**
     * @param string $branchName
     */
    public function __construct($branchName)
    {
        if (!is_string($branchName)) {
            throw new InvalidArgumentException(
                '$branchName parameter should be a string, received: ' . var_export($branchName, true)
            );
        }

        $this->branchName = $branchName;
    }

    public function __toString()
    {
        return $this->branchName;
    }
}
