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
class AbandonedCartScheduleSent extends comSimpleObject
{
    /**
     * Gets the abandoned cart order
     *
     * @return \AbandonedCartOrder|null
     */
    public function getOrder()
    {
        return $this->adapter->getObject('AbandonedCartOrder', $this->get('order'));
    }

    /**
     * Marks the record as sent
     * 
     * @return bool
     */
    public function send()
    {
        $this->set('sent', 1);
        $this->set('sent_on', time());
        $this->save();

        return true;
    }
}
