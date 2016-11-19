<?php

namespace Cart\Tests\Resources;

use Cart\Contracts\SessionContractInterface;

/**
 * Fake Session class for CartTest->changeSessionSystem() test
 *
 * @package Cart\Tests\Resources
 */
class Session implements SessionContractInterface
{
    public function put($key, $value)
    {
    }

    public function get($key)
    {
    }

    public function has($key)
    {
    }

    public function forget($key)
    {
    }

    public function flush()
    {
    }
}
