<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;
use modmore\Commerce\Admin\Widgets\TextWidget;

/**
 * Class Delete
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule
 */
class Delete extends Page
{
    public $key = 'abandonedcarts/schedule/delete';
    public $title = 'commerce.delete';

    public function setUp()
    {
        $abandonedCartId = (int)$this->getOption('id', 0);
        $abandonedCart = $this->adapter->getObject('AbandonedCartSchedule', ['id' => $abandonedCartId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);

        if ($abandonedCart) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce.delete'
            ]);
            $widget->setRecord($abandonedCart);
            $widget->setClassKey('AbandonedCartSchedule');
            $widget->setFormAction($this->adapter->makeAdminUrl('abandonedcarts/schedule/delete', ['id' => $abandonedCart->get('id')]));
            $widget->setUp();
        } else {
            $widget = (new TextWidget($this->commerce, ['text' => 'Abandoned cart schedule not found.']))->setUp();
        }

        $section->addWidget($widget);
        $this->addSection($section);

        return $this;
    }
}