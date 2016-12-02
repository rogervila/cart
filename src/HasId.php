<?php

namespace Cart;

trait HasId
{
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
}
