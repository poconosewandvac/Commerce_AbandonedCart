<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers;

use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\FormWidget;

/**
 * Class Form
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers
 */
class Form extends FormWidget
{
    protected $classKeyAction = 'abandoned_cart_user';
    protected $classKey = 'AbandonedCartUser';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new NumberField($this->commerce, [
            'name' => 'user',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.customer'),
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'email',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.email'),
        ]);

        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'subscribed',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.subscribed'),
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if ($this->record->get('id')) {
            return $this->adapter->makeAdminUrl('abandonedcarts/customers/update', ['id' => $this->record->get('id')]);
        }
    }
}