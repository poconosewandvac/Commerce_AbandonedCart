<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Overview extends Page
{
    public $key = 'abandonedcarts';
    public $title = 'Abandoned Carts';

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