<?php

namespace Cart\Transformers;

use Cart\Cart;
use Cart\Item;

/**
 * @property \Cart\Contracts\SessionContractInterface $session
 */
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


    /**
     * @param $id
     *
     * @return $this
     */
    protected function createOrRetrieve($id)
    {
        // Get the Cart from the session
        if ( ! $this->retrieveFromSession($id)) {
            // If its not set on the session, create it with the current ID
            return $this->setId($id);
        }

        return $this;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function retrieveFromSession($id)
    {
        if (is_null($this->session)) {
            return false;
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $cartFromSession = $this->session->get($id);

        if ($cartFromSession instanceof Cart) {
            $properties = $this->properties();

            foreach ($properties as $property) {
                $this->{$property} = $cartFromSession->{$property};
            }

            return true;
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return bool|array
     * @throws \Exception
     */
    protected function getCartItemById($id)
    {
        // Filter the items array, returning the Item object that has the id $id
        $result = array_filter(
            $this->items,
            function (Item $current) use ($id) {
                return $current->id() == $id;
            }
        );

        // if there is more than one result, something went wrong because IDs must be unique
        if (count($result) > 1) {
            throw new \Exception('There is more than one item with the id "' . $id . '" on the cart with id "' . $this->id . '"');
        }

        // If the item exists, the filter will return an array with one value.
        if (isset($result[0])) {
            return $result[0];
        }

        // If the result is an empty array, return false
        return false;
    }

    /**
     * @return mixed|string
     */
    protected function setDefaultID()
    {
        // If is already stored, return it
        if ($this->session->has(self::DEFAULT_ID_KEY)) {
            return $this->session->get(self::DEFAULT_ID_KEY);
        }

        // If not, create a new one and store it
        $id = uniqid('_cart_');
        $this->session->put(self::DEFAULT_ID_KEY, $id);

        return $id;
    }
}
