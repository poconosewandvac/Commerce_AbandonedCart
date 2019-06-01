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
class AbandonedCartUser extends comSimpleObject
{
    /**
     * Get the send to
     *
     * @return string
     */
    public function getSendTo()
    {
        return $this->get('email');
    }

    /**
     * Gets the MODX user
     *
     * @return \xPDOSimpleObject|null
     */
    public function getUser()
    {
        return $this->adapter->getObject('modUser', $this->get('user'));
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isSubscribed()
    {
        return (bool) $this->get('subscribed');
    }
}
