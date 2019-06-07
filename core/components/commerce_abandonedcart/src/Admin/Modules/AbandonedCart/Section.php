<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;
use modmore\Commerce\Admin\Section as CommerceSection;

/**
 * Class Section
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart
 */
class Section extends CommerceSection
{
    public function setUp()
    {
        return $this;
    }

    public function getTitle()
    {
        return $this->adapter->lexicon('commerce_abandonedcarts');
    }
}