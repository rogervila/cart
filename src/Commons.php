<?php

namespace Cart;

/**
 * Common methods
 * @package Cart
 */
trait Commons
{

    // ID

    /**
     * @var string
     */
    protected $id;

    /**
     * @param null $value
     *
     * @return mixed
     */
    public function id($value = null)
    {
        if ( ! is_null($value)) {
            $this->setId($value);
        }

        return $this->getId();
    }

    /**
     * @return mixed
     */
    protected function getId()
    {
        return $this->id;
    }

    // PROPERTIES

    /**
     * @return array
     */
    public function properties()
    {
        return array_keys(get_object_vars($this));
    }
}
