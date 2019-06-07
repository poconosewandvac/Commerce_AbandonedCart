<?php

/**
 * Abandoned Cart for Commerce.
 *
 * Copyright 2019 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_abandonedcart
 * @license See core/components/commerce_abandonedcart/docs/license.txt
 */
class AbandonedCartModel
{
    /** @var \Commerce $commerce */
    public $commerce;

    /** @var \modmore\Commerce\Adapter\AdapterInterface $adapter */
    public $adapter;

    /**
     * Initialize modX, Commerce, and user
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(\Commerce $commerce, array $config = array())
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;

        $corePath = $this->adapter->getOption('commerce_abandonedcart.core_path', $config, $this->adapter->getOption('core_path') . 'components/commerce_abandonedcart/');
        $assetsUrl = $this->adapter->getOption('commerce_abandonedcart.assets_url', $config, $this->adapter->getOption('assets_url') . 'components/commerce_abandonedcart/');
        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'controllersPath' => $corePath . 'controllers/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'baseUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ]);
        
        // Add packages
        $this->adapter->loadPackage('commerce_abandonedcart', $this->config['modelPath']);
    }

    /**
     * Get an abandoned cart user
     *
     * @param \comOrder $order
     * @return \comSimpleObject|null
     */
    public function getUser(\comOrder $order)
    {
        /** @var \comOrderAddress $address */
        $address = $order->getBillingAddress();
        $email = $address->get('email');
        $user = $order->get('user');

        $q = $this->adapter->newQuery('AbandonedCartUser');
        $q->where([
            ['user:=' => $user],
            ['OR:email:=' => $email]
        ]);

        return $this->adapter->getObject('AbandonedCartUser', $q);
    }

    /**
     * Get abandoned cart order for an order
     *
     * @param \comOrder $order
     * @return \comSimpleObject|null
     */
    public function getOrder(\comOrder $order)
    {
        return $this->adapter->getObject('AbandonedCartOrder', [
            'order' => $order->get('id')
        ]);
    }

    /**
     * Add a user from an order
     *
     * @param \comOrder $order
     * @return void
     */
    public function addUser(\comOrder $order)
    {
        /** @var \comOrderAddress $address */
        $address = $order->getBillingAddress();

        $user = $this->adapter->newObject('AbandonedCartUser');
        $user->fromArray([
            'user' => $order->get('user'),
            'email' => $address->get('email')
        ]);
        $user->save();
    }

    /**
     * Add an abandoned cart order for an order
     *
     * @param \comOrder $order
     * @return void
     */
    public function addOrder(\comOrder $order)
    {
        /** @var \AbandonedCartUser $user */
        $user = $this->getUser($order);
        // Don't add orders for user's who are unsubscribed
        if ($user && !$user->isSubscribed()) {
            return;
        }

        $abandonedCartOrder = $this->adapter->newObject('AbandonedCartOrder');
        $abandonedCartOrder->fromArray([
            'user' => $order->get('user'),
            'order' => $order->get('id'),
            'added_on' => time(),
        ]);
        $abandonedCartOrder->save();        
    }
}
