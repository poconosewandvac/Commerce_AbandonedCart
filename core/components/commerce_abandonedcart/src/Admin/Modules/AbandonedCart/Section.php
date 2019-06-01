<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;
use modmore\Commerce\Admin\Section as CommerceSection;

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