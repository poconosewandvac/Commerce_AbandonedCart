<?php

namespace PoconoSewVac\AbandonedCart\Frontend;

use PoconoSewVac\AbandonedCart\Repositories\CartRepository;

/**
 * Class PreviousOrder
 * @package PoconoSewVac\AbandonedCart\Frontend
 */
final class PreviousOrder
{
    /**
     * @var \Commerce
     */
    protected $commerce;

    /**
     * @var \modmore\Commerce\Adapter\AdapterInterface|\modmore\Commerce\Adapter\Revolution
     */
    protected $adapter;

    /**
     * @var \AbandonedCartOrder $cart
     */
    protected $cart;

    /**
     * Runner constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce, \AbandonedCartOrder $cart)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
        $this->cart = $cart;
    }

    /**
     * Create an instance of PreviousOrder from a secret
     *
     * @param \Commerce $commerce
     * @param string $secret
     * @return self|null
     */
    public static function fromSecret(\Commerce $commerce, $secret)
    {
        $cartRepository = new CartRepository($commerce);

        if ($cart = $cartRepository->getBySecret($secret)) {
            echo 'found!';
            return new self($commerce, $cart);
        }

        return null;
    }

    /**
     * Persists secret supplied to session and cookie
     * Adapted from the persistOrderIdToSession method in comOrder
     *
     * @param \comOrder $order
     * @return void
     */
    private function persistSecret(\comOrder $order)
    {
        $id = $order->get('id');
        $_SESSION[\comOrder::SESSION_NAME] = $id;

        $cookieDomain = $this->adapter->getOption('session_cookie_domain', null, '');
        $cookiePath = $this->adapter->getOption('session_cookie_path', null, MODX_BASE_URL);
        if (empty($cookiePath)) {
            $cookiePath = $this->adapter->getOption('base_url', null, MODX_BASE_URL);
        }

        $cookieSecure = (bool) $this->adapter->getOption('session_cookie_secure', null, false);
        $cookieLifetime = (int) $this->adapter->getOption('session_cookie_lifetime', null, 0);
        $cookieExpiration = time() + $cookieLifetime;

        if (!headers_sent()) {
            $secret = $order->get('secret');
            setcookie(\comOrder::COOKIE_NAME, $secret, $cookieExpiration, $cookiePath, $cookieDomain, $cookieSecure, true);
        } elseif ($this->commerce->getMode() !== \Commerce::MODE_UNIT_TEST) {
            $this->adapter->log(1, '[AbandonedCart] Could not set cookie ' . \comOrder::COOKIE_NAME . ' with the order id because headers were already sent!');
        }
    }

    /**
     * Restore the abandoned cart to the user's session
     *
     * @return \comOrder
     */
    public function restore()
    {
        $order = $this->cart->getOrder();
        $this->persistSecret($order);

        return \comOrder::loadUserOrder($this->commerce);
    }
}