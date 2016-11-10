<?php

namespace Cart;

/**
 * Class Item
 * @package Cart
 */
class Item
{
    use Commons;

    /**
     * @var string
     */
    protected $name;

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
            $this->parseFields($idOrArray);
        } else {
            $this->id = $idOrArray;
        }
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


    // NAME

    /**
     * @param null $value
     *
     * @return int
     */
    public function name($value = null)
    {
        if ( ! is_null($value)) {
            $this->setName($value);
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
     * @return int
     */
    protected function getName()
    {
        return $this->name;
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
        if (is_int($quantity)) {
            $this->quantity = $quantity;

            return $this;
        }

        throw new \Exception('Item->quantity() expects an integer');
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
     * @return string
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
        // Replace 9,99 by 9.99
        if (is_string($price)) {
            $price = str_replace(',', '.', $price);
        }

        // Integers are expected to be in cents
        if (is_int($price)) {
            // Convert it to float
            $price = floatval($price);

            // Divide in order to get the cents
            $price = $price / 100;

            // Format it to show the cents
            $price = number_format($price, 2, '.', '');
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
     * @param $fields
     */
    protected function parseFields($fields)
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key}($value);
            } else {
                $this->fields[$key] = $value;
            }
        }
    }

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
     * @param $array
     *
     * @return $this
     * @throws \Exception
     */
    protected function setFields($array)
    {
        if (is_array($array)) {
            $this->fields = $array;

            return $this;
        }

        throw new \Exception('Item->setFields() expects an array. Argument given: ' . json_encode($array));
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return $this->fields;
    }
}
