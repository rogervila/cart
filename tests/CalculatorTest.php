<?php

namespace Cart\Tests;

use Cart\Cart;
use Cart\Item;
use Money\Money;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        parent::setUp();
    }

    /** @test */
    public function calculateCartSubtotalWithoutCurrency()
    {
        $cart = new Cart();

        $items = [
            new Item([
                'id' => uniqid(),
                'price' => 1000
            ]),
            new Item([
                'id' => uniqid(),
                'price' => '5,50'
            ]),
            new Item([
                'id' => uniqid(),
                'price' => '4.50'
            ])
        ];
        $expectedResult = 20.00;


        $cart->add($items);

        $this->assertEquals($expectedResult, $cart->subtotal());
    }

    /** @test */
    public function calculateCartSubtotalWithCurrency()
    {
        $cart = new Cart();
        $cart->currency('EUR');

        $items = [
            new Item([
                'id' => uniqid(),
                'price' => 1000
            ]),
            new Item([
                'id' => uniqid(),
                'price' => '5,50'
            ]),
            new Item([
                'id' => uniqid(),
                'price' => '4.50'
            ])
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $expectedResult = Money::EUR(2000);

        $cart->add($items);

        $this->assertEquals($expectedResult, $cart->subtotal());
    }
}