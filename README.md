# Cart

**DO NOT USE IT ON PRODUCTION BEFORE 1.0.0 IS TAGGED**

[![Travis Build Status](https://travis-ci.org/rogervila/cart.svg?branch=master)](https://travis-ci.org/rogervila/cart)
[![Code Climate](https://codeclimate.com/github/rogervila/cart/badges/gpa.svg)](https://codeclimate.com/github/rogervila/cart)
[![Codeclimate Test Coverage](https://codeclimate.com/github/rogervila/cart/badges/coverage.svg)](https://codeclimate.com/github/rogervila/cart/coverage)
[![Codeclimate Issue Count](https://codeclimate.com/github/rogervila/cart/badges/issue_count.svg)](https://codeclimate.com/github/rogervila/cart)
[![StyleCI](https://styleci.io/repos/73286250/shield)](https://styleci.io/repos/73286250)
[![Appveyor Build status](https://ci.appveyor.com/api/projects/status/xs0jrfxt0f1s3y0b/branch/master?svg=true)](https://ci.appveyor.com/project/roger-vila/cart/branch/master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b2bd4592-eaed-4d50-bec5-aac9acded7b4/big.png)](https://insight.sensiolabs.com/projects/b2bd4592-eaed-4d50-bec5-aac9acded7b4)

Cart is based on sessions, and follows the Fowler's Money pattern.

**Main features**.

  - Currency Management
  - OOP
  - Custom session/cache management
  - Framework agnostic
  - Easy integration

## Install

```sh
$ composer require rogervila/cart
```

## Setup

Cart has two basic objects: `Cart` and `Item` 

### Create a Cart

The cart constructor accepts an ID

```php
use Cart\Cart;

$cart = new Cart(); // generates an automatic ID
// OR
$cart = new Cart('myCustomId');
```

### Retrieve a Cart

If it exists, the Cart will be retrieved from the session by passing it's ID

```php
$cart = new Cart('myCustomId'); // If it exists on the session, retrieves it instead of creating a new one
```

### Change the Cart ID
```php
$cart = new Cart(); // generates an automatic ID
$cart->id('myCustomID'); // Changes the ID
```

> When the cart id changes, the old session is not deleted

### Add a currency

By default, Cart will work with float numbers if a currency is not set

In order to add a currency, just add this 

```php
$cart = new Cart();
$cart->currency('EUR'); // add an ISO4217 currency
```

### Create Items

When an Item is created, it **must** receive a unique ID

```php
use Cart\Item;

$item = new Item('mandatoryUniqueId');
```

### add Item data

Instead of passing only the ID, an array with data can be passed.

```php
$item = new Item([
    'id' => 'uniqueId',
    'name' => 'Banana',
    'quantity' => 1, // must be an integer
    'price' => '0.99' // it accepts strings and integers
]);
```

### add Item custom data

In order to add custom fields, a `fields()` method is provided

```php
$fields = [
    'foo' => 'bar'
]

$item->fields($fields);
```

> When the item price is set with an integer, it will be parsed as cents, so `(int) 999` will be parsed as `(string) '9.99'`

Also, Item data can be added with fluent
```php
    $item = new Item(123);
    $item->quantity(2)->price('0.99')->name('Banana');
```

### Add Items to the cart

If the item does not have a quantity, it will be set to 1

```php
$items = [
    new Item('id1'),
    new Item('id2'),
]

$cart->add($items);

// OR

$cart->add($item1)->add($item2); // etc...
```

### Get subtotal

Gets the sum from all Cart Items

```php
var_dump($cart->subtotal());
```


### Add Fees

Fees can have a percentage or a fixed value

TODO

### Add Conditions

TODO

### Get total

Gets the final result, after applying Item conditions, Cart conditions and Fees

TODO

## Todos

 - Full documentation
 - Allow price conversion when the currency changes
 - Choose between automatic and manual conversion
 - Update the cart items currency when the Cart currency is changed
 - Integrate Conditions (discounts, coupons, etc) with custom rules
 - Add Fees (Taxes, Shipping, etc)
 - More tests

## License

MIT
