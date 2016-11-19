<?php

namespace Cart\Contracts;

interface SessionContractInterface
{
    public function put($key, $value);

    public function get($key);

    public function has($key);

    public function flush();

    public function forget($key);
}
