<?php

namespace Cart\Tests;

use Cart\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        parent::setUp();
    }

    /** @test */
    public function createItem()
    {
        $item = new Item('123');

        // Is an Item
        $this->assertInstanceOf('Cart\Item', $item);

        // Check Properties
        foreach (['id', 'name', 'quantity', 'fields', 'price'] as $property) {
            $this->assertObjectHasAttribute($property, $item);
        }

        // Check methods
        foreach (['id', 'name', 'quantity', 'fields', 'price'] as $method) {
            $this->assertTrue(
                method_exists($item, $method),
                'Cart Item does not have method "' . $method . '"'
            );
        }
    }

    /** @test */
    public function itemReceivesAnIdAndReturnsIt()
    {
        $id = uniqid();
        $item = new Item($id);

        $this->assertEquals($item->id(), $id);
    }

    /** @test */
    public function itemPropertiesReturnsAnArray()
    {
        $item = new Item(uniqid());

        $this->assertTrue(
            is_array($item->properties()),
            'Cart Item properties does not return an array'
        );
    }

    /** @test */
    public function itemUpdatesItsQuantity()
    {
        $item = new Item(uniqid());
        $amount = rand(1, 10);

        $this->assertEquals($item->quantity(), 0);

        $item->quantity($amount);

        $this->assertEquals($item->quantity(), $amount);
    }

    /** @test */
    public function itemChangesItsName()
    {
        $name1 = uniqid();
        $name2 = uniqid();

        $item = new Item([
            'id' => uniqid(),
            'name' => $name1
        ]);

        $this->assertEquals($item->name(), $name1);

        $item->name($name2);

        $this->assertEquals($item->name(), $name2);
    }

    /** @test */
    public function floatPriceIsRoundedToTwoDecimals()
    {
        $item = new Item(uniqid());
        $price = 10.999;
        $item->price($price);

        $this->assertEquals($item->price(), '11.00');

        $price = 10.001;
        $item->price($price);

        $this->assertEquals($item->price(), '10.00');
    }
}
