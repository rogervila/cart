<?php

namespace Cart\Transformers;

use Cart\Item;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

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
            $price = '0';
            $item->price($price);
        }

        return $item;
    }

    protected function parsePrice(Item $item, $currency = null)
    {
        if ($currency instanceof Currency) {
            return $this->priceWithCurrency($item, $this->currency);
        }

        return $this->priceWithoutCurrency($item);
    }

    /**
     * @param Item $item
     * @param Currency $currency
     *
     * @return Item
     */
    protected function priceWithCurrency(Item $item, Currency $currency)
    {
        $item = $this->defaultPrice($item);

        // If a currency is set, convert the price into a currency value
        if ( ! is_null($this->currency)) {
            $currencies = new ISOCurrencies();
            $parser     = new DecimalMoneyParser($currencies);

            $price = $parser->parse($item->price(), $currency->getCode());

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
