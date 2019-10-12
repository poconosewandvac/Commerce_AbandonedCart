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
     * @param \AbandonedCartUser $user
     * @return void
     */
    public function addFromOrder(\comOrder $order, \AbandonedCartUser $user)
    {
        $abandonedCartOrder = $this->adapter->newObject($this->classKey);
        $abandonedCartOrder->fromArray([
            'user' => $user->get('id'), // we want to reference the abandoned cart user here, NOT modUser!
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

    /**
     * Gets pending abandoned cart orders
     *
     * @return \AbandonedCartOrder|null
     */
    public function getPending()
    {
        return $this->adapter->getCollection($this->classKey, [
            'converted' => false,
            'removed' => false,
        ]);
    }
}