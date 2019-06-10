<?php

namespace PoconoSewVac\AbandonedCart\Repositories;

/**
 * Class UserRepository
 * @package PoconoSewVac\AbandonedCart\Repositories
 */
class UserRepository extends Repository
{
    private $classKey = 'AbandonedCartUser';

    /**
     * Get an abandoned cart user
     *
     * @param \comOrder $order
     * @return \AbandonedCartUser|null
     */
    public function getByOrder(\comOrder $order)
    {
        /** @var \comOrderAddress $address */
        $address = $order->getBillingAddress();
        $email = $address->get('email');
        $user = $order->get('user');

        $q = $this->adapter->newQuery($this->classKey);
        $q->where([
            ['user:=' => $user],
            ['OR:email:=' => $email]
        ]);

        return $this->adapter->getObject($this->classKey, $q);
    }

    /**
     * Add a user from an order
     *
     * @param \comOrder $order
     * @return void
     */
    public function addFromOrder(\comOrder $order)
    {
        /** @var \comOrderAddress $address */
        $address = $order->getBillingAddress();

        $user = $this->adapter->newObject($this->classKey);
        $user->fromArray([
            'user' => $order->get('user'),
            'email' => $address->get('email')
        ]);
        $user->save();
    }
}