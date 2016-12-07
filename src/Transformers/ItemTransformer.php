<?php

namespace Cart\Transformers;

use Cart\Item;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

/**
 * @property Currency $currency
 */
trait ItemTransformer
{
    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function defaultPrice(Item $item)
    {
        // Get the current item price
        $price = $item->price();

        // If price is not numeric, set it to 0
        if ( ! is_numeric($price)) {
            $item->price('0');
        }

        return $item;
    }

    /**
     * @param string $price
     *
     * @return mixed
     */
    protected function parseStringPrice($price)
    {
        // Convert 9,99 to 9.99
        return str_replace(',', '.', $price);
    }

    /**
     * @param float $price
     * @return string
     */
    protected function parseFloatPrice($price)
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Integers are expected to be in cents
     *
     * @param int $price
     *
     * @return string
     */
    protected function parseIntegerPrice($price)
    {
        // Convert it to float
        $price = floatval($price);

        // Divide in order to get the cents
        $price = $price / 100;

        // Format it to show the cents
        return $this->parseFloatPrice($price);
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function parsePrice(Item $item)
    {
        if ($this->currency instanceof Currency) {
            return $this->priceWithCurrency($item);
        }

        return $this->priceWithoutCurrency($item);
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function priceWithCurrency(Item $item)
    {
        $item = $this->defaultPrice($item);

        // If a currency is set, convert the price into a currency value
        if ( ! is_null($this->currency)) {
            $currencies = new ISOCurrencies();
            $parser = new DecimalMoneyParser($currencies);
            $price = $parser->parse($item->price(), $this->currency->getCode());

            $item->price($price);
        }

        return $item;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function priceWithoutCurrency(Item $item)
    {
        // If a currency is not set, store the item price as a float
        $item->price(floatval($item->price()));

        return $item;
    }

    /**
     * Set the quantity to 1 if there is no quantity
     *
     * @param Item $item
     *
     * @return Item
     */
    protected function atLeastOneQuantity(Item $item)
    {
        if (is_null($item->quantity())) {
            $item->quantity(1);
        }

        return $item;
    }
}
