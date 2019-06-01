<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;
use modmore\Commerce\Admin\Widgets\TextWidget;

class Delete extends Page
{
    public $key = 'abandonedcarts/delete';
    public $title = 'commerce.delete';

    public function setUp()
    {
        $abandonedCartId = (int)$this->getOption('id', 0);
        $abandonedCart = $this->adapter->getObject('AbandonedCartOrder', ['id' => $abandonedCartId]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);

        if ($abandonedCart) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce.delete'
            ]);
            $widget->setRecord($abandonedCart);
            $widget->setClassKey('AbandonedCartOrder');
            $widget->setFormAction($this->adapter->makeAdminUrl('abandonedcarts/delete', ['id' => $abandonedCart->get('id')]));
            $widget->setUp();
        } else {
            $widget = (new TextWidget($this->commerce, ['text' => 'Abandoned cart not found.']))->setUp();
        }

        $section->addWidget($widget);
        $this->addSection($section);

        return $this;
    }
}