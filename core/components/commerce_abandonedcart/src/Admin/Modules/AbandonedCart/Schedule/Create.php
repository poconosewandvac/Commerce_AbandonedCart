<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

/**
 * Class Create
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule
 */
class Create extends Page
{
    public $key = 'abandonedcarts/schedule/create';
    public $title = 'commerce_abandonedcart.add_message';

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);

        $section->addWidget((new Form($this->commerce, ['id' => 0]))->setUp());
        $this->addSection($section);

        return $this;
    }
}