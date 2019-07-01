<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

/**
 * Class Overview
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule
 */
class Overview extends Page
{
    public $key = 'abandonedcarts/schedule';
    public $title = 'Abandoned Carts Schedule';

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