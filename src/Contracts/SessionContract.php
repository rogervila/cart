<?php

namespace Cart\Contracts;

interface SessionContract
{
    public function put($key, $value);

    public function get($key);

    public function has($key);

    public function flush();

    public function forget($key);
}