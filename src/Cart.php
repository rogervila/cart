<?php

namespace Cart;

use Cart\Contracts\SessionContract;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

/**
 * Class Cart
 * @package Cart
 */
class Cart
{
    use Commons;

    // Default ID key for the Cart
    const DEFAULT_ID_KEY = '_defaultCartIdKey';

    /**
     * @var SessionContract
     */
    protected $session;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * Cart constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        // The default session system is included
        $this->session = new Session();

        // Set a default ID if no value is passed
        if (is_null($id)) {
            return $this->setId();
        }

        // Retrieve it or create a new cart
        return $this->createOrRetrieve($id);
    }

    // SELF

    /**
     * @param $id
     *
     * @return $this
     */
    protected function createOrRetrieve($id)
    {
        // Get the Cart from the session
        if ( ! $this->retrieveFromSession($id)) {
            // If its not set on the session, create it with the current ID
            var_dump('new cart');

            return $this->setId($id);
        }
        var_dump('cart from session');

        return $this;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function retrieveFromSession($id)
    {
        $cartFromSession = $this->session->get($id);

        if ($cartFromSession instanceof Cart) {
            $properties = $this->properties();

            foreach ($properties as $property) {
                $this->{$property} = $cartFromSession->{$property};
            }

            return true;
        }

        return false;
    }

    // ID

    /**
     * @param null $id
     *
     * @return $this
     */
    protected function setId($id = null)
    {
        if (is_null($id)) {
            $this->id = $this->setDefaultID();
        } else {
            $this->id = $id;
        }

        // A new session row will be added with the new ID.
        $this->session->put($this->id, $this);

        // IMPORTANT: if the ID is changed, the old row will not be deleted automatically.
        return $this;
    }

    /**
     * @return mixed|string
     */
    protected function setDefaultID()
    {
        // If is already stored, return it
        if ($this->session->has(self::DEFAULT_ID_KEY)) {
            return $this->session->get(self::DEFAULT_ID_KEY);
        }

        // If not, create a new one and store it
        $id = uniqid('_cart_');
        $this->session->put(self::DEFAULT_ID_KEY, $id);

        return $id;
    }

    // Session

    /**
     * @param null $value
     *
     * @return mixed
     */
    public function session($value = null)
    {
        if ( ! is_null($value)) {
            $this->setSession($value);
        }

        return $this->session;
    }


    /**
     * Change the Session system
     *
     * @param SessionContract $contract
     *
     * @return $this
     */
    protected function setSession(SessionContract $contract)
    {
        $this->session = $contract;

        return $this;
    }

    // ITEMS

    /**
     * Add an Item or an array of Items
     *
     * @param $arrayOrItem
     *
     * @return $this|Cart
     * @throws \Exception
     */
    public function add($arrayOrItem)
    {
        if ($arrayOrItem instanceof Item) {
            return $this->setItem($arrayOrItem);
        }

        if (is_array($arrayOrItem)) {
            foreach ($arrayOrItem as $item) {
                if ($item instanceof Item) {
                    $this->setItem($item);
                }
            }

            return $this;
        }

        throw new \Exception('Value passed to Cart->add() is not an array nor an Item. Value passed: ' . json_encode($arrayOrItem));
    }

    /**
     * @param Item $item
     *
     * @return $this
     * @throws \Exception
     */
    protected function setItem(Item $item)
    {
        // Check that there is no other item with the same ID
        if ( ! $this->has($item)) {

            // The item MUST have an ID
            if (is_null($item->id()) || $item->id() === '') {
                throw new \Exception("An item without ID can't be added to the cart. The item: " . json_encode($item));
            }

            // Parse its price and quantity
            $item = $this->parseItem($item);

            // Add it
            array_push($this->items, $item);

            // Append the full object to the session
            $this->session->put($this->id, $this);
        }

        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Item
     */
    protected function parseItem(Item $item)
    {
        // Set at least one item on the cart
        if (is_null($item->quantity())) {
            $item->quantity(1);
        }

        // Get the current item price
        $price = $item->price();

        // If price is not numeric, set it to 0
        if ( ! is_numeric($price)) {
            $price = '0';
        }

        // If a currency is set, convert the price into a currency value
        if ( ! is_null($this->currency)) {
            $currencies = new ISOCurrencies();
            $parser     = new DecimalMoneyParser($currencies);

            $price = $parser->parse($price, $this->currency->getCode());

            $item->price($price);
        } else {
            // If a currency is not set, store the item price as a float
            $item->price(floatval($price));
        }

        return $item;
    }

    /**
     * If no value is passed returns the current cart items. Else, acts like an alias of add()
     *
     * @param null $itemOrItems
     *
     * @return array|Cart
     */
    public function items($itemOrItems = null)
    {
        if (is_null($itemOrItems)) {
            return $this->items;
        }

        return $this->add($itemOrItems);
    }

    /**
     * @param $id
     *
     * @return array|bool
     */
    public function item($id)
    {
        return $this->getItemById($id);
    }

    /**
     * Looks for the items that contain the same values as the $item
     *
     * @param Item $item
     *//*
    public function search(Item $item)
    {
        // TODO
    }*/

    /**
     * @param Item $item
     *
     * @return $this
     */
    public function update(Item $item)
    {
        // The item MUST have an ID
        $currentItem = $this->getItemById($item->id());

        if ($currentItem instanceof Item) {

            // returns property names as an array
            $itemProperties = $item->properties();

            // Update every $currentItem property with the $item ones
            foreach ($itemProperties as $property) {
                $value = $item->{$property}();

                if ( ! is_null($value) && $value !== '') {
                    $currentItem->{$property}($value);
                }
            }

            // Append the full object to the session
            $this->session->put($this->id, $this);
        }

        return $this;
    }

    /**
     * If getItemById returns a boolean, it has not found the Item
     *
     * @param Item $item
     *
     * @return bool
     */
    public function has(Item $item)
    {
        return ! is_bool($this->getItemById($item->id()));
    }

    /**
     * @param $id
     *
     * @return bool|array
     * @throws \Exception
     */
    protected function getItemById($id)
    {
        // Filter the items array, returning the Item object that has the id $id
        $result = array_filter(
            $this->items,
            function ($current) use ($id) {
                /** @noinspection PhpUndefinedMethodInspection */
                return $current->id() == $id;
            }
        );

        // if there is more than one result, something went wrong because IDs must be unique
        if (count($result) > 1) {
            throw new \Exception('There is more than one item with the id "' . $id . '" on the cart with id "' . $this->id . '"');
        }

        // If the item exists, the filter will return an array with one value.
        if (isset($result[0])) {
            return $result[0];
        }

        // If the result is an empty array, return false
        return false;
    }


    // CURRENCY

    /**
     * @param null $value
     *
     * @return Currency
     */
    public function currency($value = null)
    {
        if ( ! is_null($value)) {
            $this->setCurrency($value);
        }

        return $this->currency;
    }

    /**
     * @param $currency
     *
     * @return $this
     */
    protected function setCurrency($currency)
    {
        $currencies = new ISOCurrencies();

        if ($currencies->contains(new Currency($currency))) {
            $this->currency = new Currency($currency);
        }

        return $this;
    }

    // RESULTS

    /**
     * @return float|Money
     */
    public function subtotal()
    {
        if ( ! is_null($this->currency)) {
            return $this->calculateSubtotalWithCurrency();
        }

        return $this->calculateSubtotalWithoutCurrency();
    }

    /**
     * @return Money
     */
    protected function calculateSubtotalWithCurrency()
    {
        $result = new Money(0, $this->currency);

        foreach ($this->items as $item) {
            $result = $result->add($item->price());
        }

        return $result;
    }

    /**
     * @return float
     */
    protected function calculateSubtotalWithoutCurrency()
    {
        $result = 0.0;

        foreach ($this->items as $item) {
            $result += $item->price();
        }

        return $result;
    }

    /*
    public function total()
    {
        // TODO
    }*/
}
