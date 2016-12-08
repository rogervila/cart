<?php

namespace Cart\Tests;

use Cart\Cart;
use Cart\Item;
use Cart\Tests\Resources\Session;


class CartTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        parent::setUp();
    }

    /** @test */
    public function createCart()
    {
        $cart = new Cart();

        // Is a Cart
        $this->assertInstanceOf('Cart\Cart', $cart);

        // Check Properties
        foreach (['id', 'items', 'session', 'currency'] as $property) {
            $this->assertObjectHasAttribute($property, $cart);
        }

        // Check methods
        foreach (['has', 'items', 'add', 'update', 'session'] as $method) {
            $this->assertTrue(
                method_exists($cart, $method),
                'Cart does not have method "' . $method . '"'
            );
        }

        // Check constants
        foreach (['DEFAULT_ID_KEY'] as $constant) {
            $cart = new \ReflectionClass(Cart::class);
            $this->assertArrayHasKey($constant, $cart->getConstants());
        }
    }

    /** @test */
    public function createCartWithCustomId()
    {
        $id = uniqid();
        $cart = new Cart($id);

        $this->assertEquals($cart->id(), $id);
    }

    /** @test */
    public function changeCartId()
    {
        $cart = new Cart();
        $id = uniqid();

        $this->assertNotEquals($cart->id(), $id);

        $cart->id($id);

        $this->assertEquals($cart->id(), $id);
    }

    /** @test */
    public function setCardCurrency()
    {
        $cart = new Cart();

        $cart->currency('EUR');

        $this->assertEquals(get_class($cart->currency()), 'Money\Currency');
    }

    /** @test */
    public function getStoredCard()
    {
        $id = uniqid();
        $currency = 'EUR';
        $cart = new Cart($id);

        $cart->currency($currency);

        unset($cart);

        $cart2 = new Cart($id);

        $this->assertEquals(get_class($cart2->currency()), 'Money\Currency');
        $this->assertEquals($cart2->currency(), $currency);
    }

    /** @test */
    public function changeSessionSystem()
    {
        $cart = new Cart();

        // The default session class is set
        $this->assertInstanceOf('Cart\Session', $cart->session());

        $cart->session(new Session());

        $this->assertInstanceOf('\Cart\Tests\Resources\Session', $cart->session());
    }

    /** @test */
    public function cartPropertiesReturnsAnArray()
    {
        $cart = new Cart();

        $this->assertTrue(
            is_array($cart->properties()),
            'Cart properties does not return an array'
        );
    }

    /** @test */
    public function addItem()
    {
        $cart = new Cart();
        $id = uniqid();
        $item = new Item($id);

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[0]->id(), $id);
    }

    /** @test */
    public function addMultipleItems()
    {
        $cart = new Cart();
        $id0 = uniqid();
        $id1 = uniqid();
        $id2 = uniqid();
        $items = [
            new Item($id0),
            new Item($id1),
            new Item($id2),
        ];

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($items);

        $cartItems = $cart->items();

        $this->assertEquals(count($cartItems), 3);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cartItems[0]->id(), $id0);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cartItems[1]->id(), $id1);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cartItems[2]->id(), $id2);
    }

    /** @test */
    public function addItemsWithFluent()
    {
        $cart = new Cart();
        $id0 = uniqid();
        $id1 = uniqid();
        $id2 = uniqid();

        $cart->add(new Item($id0))->add(new Item($id1))->add(new Item($id2));

        $this->assertEquals(count($cart->items()), 3);
    }

    /** @test */
    public function updateCartItem()
    {
        $cart = new Cart();
        $id = uniqid();
        $item = new Item($id);
        $quantity = rand(1, 100);

        $cart->add($item);
        $item->quantity($quantity);
        $cart->update($item);

        $this->assertEquals(count($cart->items()), 1);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue($cart->item($id)->quantity() === $quantity);
    }

    /** @test */
    public function updateCartItemWithAnotherItemObject()
    {
        $cart = new Cart();
        $id = uniqid();
        $quantity = rand(1, 10);

        $cart->add(new Item($id))->update(new Item([
            'id' => $id,
            'quantity' => $quantity
        ]));

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[0]->quantity(), $quantity);
    }

    /** @test */
    public function whenAddingAnItemTwiceItGetsUpdated()
    {
        $cart = new Cart();
        $item = new Item(uniqid());

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);

        /** @noinspection PhpUndefinedFieldInspection */
        $item->foo = 'bar';
        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);
        $this->assertObjectHasAttribute('foo', $cart->items()[0]);
    }

    /** @test */
    public function getCartItem()
    {
        $cart = new Cart();
        $id = uniqid();
        $item = new Item($id);

        $cart->add($item);
        $retrievedItem = $cart->item($id);

        $this->assertEquals($item, $retrievedItem);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function addCartItemsWithoutIdShouldFail()
    {
        $cart = new Cart();
        $item = new Item();

        $cart->add($item);
    }

    /** @test */
    public function addItemsWithIdsAddedFluently()
    {
        $cart = new Cart();
        $item = new Item();

        $item->id(uniqid());

        $cart->add([
            new Item(uniqid()),
            $item,
        ]);
    }
}