<?php

namespace Cart;

use Cart\Transformers\ItemTransformer;

/**
 * Class Item
 * @package Cart
 */
class Item
{
    use HasId, HasName, ReturnsProperties, ItemTransformer;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var mixed
     */
    protected $price;

    /**
     * @var array
     */
    protected $fields;

    /**
     * Item constructor.
     *
     * @param $idOrArray
     */
    public function __construct($idOrArray)
    {
        if (is_array($idOrArray)) {
            return $this->setFields($idOrArray);
        }

        return $this->id = $idOrArray;
    }

    // ID

    /**
     * @param $id
     *
     * @return $this
     */
    protected function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    // QUANTITY

    /**
     * @param null $value
     *
     * @return int
     */
    public function quantity($value = null)
    {
        if ( ! is_null($value)) {
            return $this->setQuantity($value);
        }

        return $this->getQuantity();
    }

    /**
     * @param int $quantity
     *
     * @return $this
     * @throws \Exception
     */
    protected function setQuantity($quantity)
    {
        if ( ! is_int($quantity)) {
            throw new \Exception('Item->quantity() expects an integer');
        }

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    protected function getQuantity()
    {
        return $this->quantity;
    }


    // PRICE

    /**
     * @param null $value
     *
     * @return mixed
     */
    public function price($value = null)
    {
        if ( ! is_null($value)) {
            return $this->setPrice($value);
        }

        return $this->getPrice();
    }

    /**
     * @param mixed $price
     *
     * @return $this
     * @throws \Exception
     */
    protected function setPrice($price)
    {
        if (is_string($price)) {
            $price = $this->parseStringPrice($price);
        }

        if (is_int($price)) {
            $price = $this->parseIntegerPrice($price);
        }

        $this->price = $price;

        return $this;
    }

    /**
     * @return string
     */
    protected function getPrice()
    {
        return $this->price;
    }


    // FIELDS

    /**
     * @param null $value
     *
     * @return array
     */
    public function fields($value = null)
    {
        if ( ! is_null($value)) {
            $this->setFields($value);
        }

        return $this->getFields();
    }

    /**
     * @param $fields
     *
     * @return $this
     * @throws \Exception
     */
    protected function setFields($fields)
    {
        if ( ! is_array($fields)) {
            throw new \Exception('Item->setFields() expects an array. Argument given: ' . json_encode($fields));
        }

        // Set attributes or custom fields
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key}($value);
            } else {
                $this->fields[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return $this->fields;
    }
}
