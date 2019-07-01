<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

/**
 * Class Overview
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers
 */
class Overview extends Page
{
    public $key = 'abandonedcarts/customers';
    public $title = 'Abandoned Carts Customers';

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);

        $section->addWidget(new Grid($this->commerce));
        $this->addSection($section);

        return $this;
    }
}