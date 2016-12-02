<?php

namespace Cart;

trait ReturnsProperties
{
    /**
     * @return array
     */
    public function properties()
    {
        return array_keys(get_object_vars($this));
    }
}