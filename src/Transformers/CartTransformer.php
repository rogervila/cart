<?php

namespace Cart\Transformers;

trait CartTransformer
{
    /**
     * @param null $id
     *
     * @return string
     */
    protected function handleId($id = null)
    {
        if (is_null($id)) {
            return $this->setDefaultID();
        }

        return trim($id);
    }
}