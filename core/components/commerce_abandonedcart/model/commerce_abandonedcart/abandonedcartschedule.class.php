<?php

use PoconoSewVac\AbandonedCart\Services\CartConditions;

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
class AbandonedCartSchedule extends comSimpleObject
{
    /**
     * Checks if the conditions set on the schedule are met
     * 
     * @param \AbandonedCartOrder $cart
     * @return bool
     */
    public function conditionsMet(\AbandonedCartOrder $cart)
    {
        $conditions = new CartConditions($this, $cart);

        return $conditions->areMet();
    }

    /**
     * Sends the message to the specified cart
     * 
     * @param \AbandonedCartOrder $cart
     * @return bool
     */
    public function send(\AbandonedCartOrder $cart)
    {
        /** @var \comOrder $order */
        $order = $cart->getOrder();

        /** @var \AbandonedCartUser $user */
        $user = $cart->getUser();

        /** @var \comOrderEmailMessage $message */
        $message = $this->adapter->newObject('comOrderEmailMessage');
        $message->fromArray([
            'order' => $order->get('id'),
            'content' => $this->get('content'),
            'recipient' => $user->get('email'),
            'from' => $this->get('from'),
            'created_on' => time(),
            'created_by' => 0,
        ]);

        $message->setProperties([
            'subject' => $this->get('subject'),
        ]);

        return $message->save() && $message->send();
    }
}
