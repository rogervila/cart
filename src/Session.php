<?php

namespace Cart;

use Cart\Contracts\SessionContract;

/**
 * Class Session
 * @package Cart
 */
class Session implements SessionContract
{
    /**
     * @var string
     */
    protected $name = '_cart';

    /**
     * Session constructor.
     */
    public function __construct()
    {
        if ( ! $this->isStarted()) {
            @session_start();
        }
    }

    /**
     * @return bool
     */
    private function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE ? true : false;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function put($key, $value)
    {
        $_SESSION[$this->name][$key] = serialize($value);

        return $_SESSION[$this->name][$key];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($_SESSION[$this->name][$key])) {
            return unserialize($_SESSION[$this->name][$key]);
        }

        return false;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function has($key)
    {
        return isset($_SESSION[$this->name][$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function forget($key)
    {
        unset($_SESSION[$this->name][$key]);

        return true;
    }

    /**
     * @return bool
     */
    public function flush()
    {
        $_SESSION[$this->name] = [];

        return true;
    }
}
