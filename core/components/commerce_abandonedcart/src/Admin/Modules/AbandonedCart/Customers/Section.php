<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers;

use modmore\Commerce\Admin\Section as BaseSection;

/**
 * Class Section
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers
 */
class Section extends BaseSection
{
    public function setUp()
    {
        return $this;
    }
    public function getTitle()
    {
        return $this->adapter->lexicon('commerce_abandonedcart.customer');
    }
}