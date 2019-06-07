<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

/**
 * Class Update
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers
 */
class Update extends Page {
    public $key = 'abandonedcarts/customers/update';
    public $title = 'commerce_abandonedcart.update_customer';

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $exists = $this->adapter->getCount('AbandonedCartUser', ['id' => $objectId, 'removed' => false]);

        if ($exists) {
            $section = new Section($this->commerce, [
                'title' => $this->title
            ]);

            $section->addWidget((new Form($this->commerce, ['isUpdate' => true, 'id' => $objectId]))->setUp());
            $this->addSection($section);

            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}