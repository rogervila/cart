<?php

namespace Cart\Helpers;

use Cart\Item;
use Money\Money;

trait Calculator
{
    /**
     * @param $items
     * @param $currency
     *
     * @return Money
     */
    protected function calculateSubtotalWithCurrency($items, $currency)
    {
        $result = new Money(0, $currency);

        foreach ($items as $item) {
            if ($item instanceof Item) {
                $result = $result->add($item->price());
            }
        }

        return $result;
    }

    /**
     * @param $items
     *
     * @return float|mixed
     */
    protected function calculateSubtotalWithoutCurrency($items)
    {
        $result = 0.0;

        foreach ($items as $item) {
            if ($item instanceof Item) {
                $result += $item->price();
            }
        }

        return $result;
    }
}