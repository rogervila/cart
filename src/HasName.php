<?php

namespace Cart;

trait HasName
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param null $value
     *
     * @return string
     */
    public function name($value = null)
    {
        if ( ! is_null($value)) {
            return $this->setName($value);
        }

        return $this->getName();
    }

    /**
     * @param $name
     *
     * @return $this
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }
}
