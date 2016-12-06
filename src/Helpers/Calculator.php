<?php

namespace Cart\Helpers;

use Cart\Item;
use Money\Currency;
use Money\Money;

trait Calculator
{
    /**
     * @return Money
     * @throws \Exception
     */
    protected function calculateSubtotalWithCurrency()
    {
        if ( ! $this->currency instanceof Currency) {
            throw new \Exception('$this->currency should be instance of Money\Currency, "' . get_class($this->currency) . '" given');
        }

        $result = new Money(0, $this->currency);

        foreach ($this->items as $item) {
            if ($item instanceof Item) {
                $result = $result->add($item->price());
            }
        }

        return $result;
    }

    /**
     * @return float|mixed
     */
    protected function calculateSubtotalWithoutCurrency()
    {
        $result = 0.0;

        foreach ($this->items as $item) {
            if ($item instanceof Item) {
                $result += $item->price();
            }
        }

        return $result;
    }
}
