<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule;

use modmore\Commerce\Admin\Widgets\Form\TextareaField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;
use PoconoSewVac\AbandonedCart\Admin\Widgets\Form\ConditionField;
use PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation\Email;

/**
 * Class Form
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule
 */
class Form extends FormWidget
{
    protected $classKeyAction = 'abandoned_cart_schedule';
    protected $classKey = 'AbandonedCartSchedule';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'from',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.from'),
            'validation' => [ new Required(), new Email() ]
        ]);

        $fields[] =  new TextField($this->commerce, [
            'name' => 'subject',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.subject'),
            'validation' => [ new Required() ]
        ]);

        $fields[] =  new TextField($this->commerce, [
            'name' => 'send_time',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.send_time'),
            'description' => $this->adapter->lexicon('commerce_abandonedcart.send_time_desc'),
            'validation' => [ new Required() ]
        ]);

        $fields[] = new ConditionField($this->commerce, [
            'name' => 'conditions',
            'label' => $this->adapter->lexicon('commerce.conditions'),
            'description' => $this->adapter->lexicon('commerce_abandonedcart.conditions_desc'),
        ]);

        $fields[] = new TextareaField($this->commerce, [
            'name' => 'content',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.content'),
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if ($this->record->get('id')) {
            return $this->adapter->makeAdminUrl('abandonedcarts/schedule/update', ['id' => $this->record->get('id')]);
        }

        return $this->adapter->makeAdminUrl('abandonedcarts/schedule/create');
    }
}