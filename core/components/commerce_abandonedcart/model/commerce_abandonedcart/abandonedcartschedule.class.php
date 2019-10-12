<?php

use PoconoSewVac\AbandonedCart\Services\CartCondition;

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
        // Check if message has been sent
        $sentFlag = $this->adapter->getObject('AbandonedCartScheduleSent', [
            'order' => $cart->get('id'),
            'schedule' => $this->get('id'),
            'sent' => 1
        ]);

        if ($sentFlag) {
            return false;
        }

        // Check if time is valid
        $sendTime = strtotime($this->get('send_time'), $cart->get('added_on'));
        if ($sendTime === false) {
            $this->adapter->log(1, '[AbandonedCart] Invalid time passed to strtotime "' . $sendTime . '" for schedule ' . $this->get('id'));
            return false;
        }

        if ($sendTime >= time()) {
            return false;
        }

        // Check if anything should be checked
        if (!$this->hasConditions()) {
            return true;
        }

        $conditionals = $this->get('conditions');
        foreach ($conditionals as $conditional) {
            $condition = new CartCondition($cart, $conditional);
            
            if ($condition->check()) {
                $this->adapter->log(4, '[AbandonedCart] Cart "' . $cart->get('id') . '" passed conditional for schedule ' . $this->get('id'));
                continue;
            } else {
                $this->adapter->log(4, '[AbandonedCart] Cart "' . $cart->get('id') . '" failed conditional for schedule ' . $this->get('id'));
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if schedule has any conditional logic of when to send
     * 
     * @return bool
     */
    public function hasConditions()
    {
        return (bool) count($this->get('conditions'));
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

        /** @var \AbandonedCartScheduleSent $sentFlag */
        $sentFlag = $this->adapter->newObject('AbandonedCartScheduleSent');
        $sentFlag->fromArray([
            'order' => $cart->get('id'),
            'schedule' => $this->get('id')
        ]);

        return $message->save() && $message->send() && $sentFlag->send();
    }
}
