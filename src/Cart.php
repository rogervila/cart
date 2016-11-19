<?php

namespace Cart;

use Cart\Contracts\SessionContractInterface;
use Cart\Helpers\Calculator;
use Cart\Transformers\CartTransformer;
use Cart\Transformers\ItemTransformer;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;

/**
 * Class Cart
 * @package Cart
 */
class Cart
{
    use Commons, Calculator, CartTransformer, ItemTransformer;

    // Default ID key for the Cart
    const DEFAULT_ID_KEY = '_defaultCartIdKey';

    /**
     * @var SessionContractInterface
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

    // ID

    /**
     * @param null $id
     *
     * @return $this
     */
    protected function setId($id = null)
    {
        $this->id = $this->handleId($id);

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
     * @param SessionContractInterface $contract
     *
     * @return $this
     */
    protected function setSession(SessionContractInterface $contract)
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

        if ( ! is_array($arrayOrItem)) {
            throw new \Exception('Value passed to Cart->add() is not an array nor an Item. Value passed: ' . json_encode($arrayOrItem));
        }

        foreach ($arrayOrItem as $item) {
            if ($item instanceof Item) {
                $this->setItem($item);
            }
        }

        return $this;
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
        if ($this->has($item)) {
            return $this;
        }

        // The item MUST have an ID
        if (is_null($item->id()) || $item->id() === '') {
            throw new \Exception("An item without ID can't be added to the cart. The item: " . json_encode($item));
        }

        // At least 1 quantity
        $item = $this->atLeastOneQuantity($item);

        // Price with or without currency
        $item = $this->parsePrice($item, $this->currency);

        // Add it to the Cart
        array_push($this->items, $item);

        // Append the full object to the session
        $this->session->put($this->id, $this);

        return $this;
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
        return $this->getCartItemById($id);
    }

    /**
     * @param Item $item
     *
     * @return $this
     */
    public function update(Item $item)
    {
        // The item MUST have an ID
        $currentItem = $this->getCartItemById($item->id());

        if ( ! $currentItem instanceof Item) {
            return $this;
        }

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
        return ! is_bool($this->getCartItemById($item->id()));
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
            return $this->calculateSubtotalWithCurrency($this->items, $this->currency);
        }

        return $this->calculateSubtotalWithoutCurrency($this->items);
    }
}
