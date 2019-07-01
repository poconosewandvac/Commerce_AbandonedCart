<?php

namespace PoconoSewVac\AbandonedCart\Repositories;

/**
 * Class CartRepository
 * @package PoconoSewVac\AbandonedCart\Repositories
 */
class CartRepository extends Repository
{
    private $classKey = 'AbandonedCartOrder';

    /**
     * Add an abandoned cart order for an order
     *
     * @param \comOrder $order
     * @return void
     */
    public function addFromOrder(\comOrder $order)
    {
        $abandonedCartOrder = $this->adapter->newObject($this->classKey);
        $abandonedCartOrder->fromArray([
            'user' => $order->get('user'),
            'order' => $order->get('id'),
            'added_on' => time(),
        ]);
        $abandonedCartOrder->save();
    }

    /**
     * Get abandoned cart order for an order
     *
     * @param \comOrder $order
     * @return \AbandonedCartOrder|null
     */
    public function getByOrder(\comOrder $order)
    {
        return $this->adapter->getObject($this->classKey, [
            'order' => $order->get('id')
        ]);
    }
}