<?php

namespace spec\Drupal\Ignite\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use InvalidArgumentException;

class PathSpec extends ObjectBehavior
{
    function it_throws_exception_when_created_from_something_that_s_not_a_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", [123]);
    }

    function it_casts_to_string()
    {
        $this->beConstructedWith("foobar");

        $this->__toString()->shouldReturn("foobar");
    }
}
