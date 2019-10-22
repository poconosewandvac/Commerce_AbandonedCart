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
class AbandonedCartOrder extends comSimpleObject
{
    use \modmore\Commerce\Traits\SoftDelete;

    /**
     * Gets the abandoned cart user
     *
     * @return \AbandonedCartUser|null
     */
    public function getUser()
    {
        return $this->adapter->getObject('AbandonedCartUser', $this->get('user'));
    }

    /**
     * Gets the abandoned cart order
     *
     * @return \comOrder|null
     */
    public function getOrder()
    {
        return $this->adapter->getObject('comOrder', $this->get('order'));
    }

    /**
     * Get the sent message count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->get('msg_count');
    }

    /**
     * Check if abandoned cart order was converted
     *
     * @return boolean
     */
    public function isConverted()
    {
        return (bool) $this->get('converted');
    }

    /**
     * Get datetime order was added on
     *
     * @return \DateTime
     */
    public function getAddedOn()
    {
        return new \DateTime('@' . $this->get('added_on'));
    }

    /**
     * Mark the abandoned cart order as converted (customer purchased)
     *
     * @return void
     */
    public function markConverted()
    {
        $this->set('converted', true);
        $this->set('converted_on', time());
    }
}
