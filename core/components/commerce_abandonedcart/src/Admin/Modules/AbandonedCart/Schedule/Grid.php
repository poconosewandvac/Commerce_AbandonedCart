<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule;

use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;

/**
 * Class Grid
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule
 */
class Grid extends GridWidget
{
    public $key = 'abandonedcarts/schedule';
    public $defaultSort = 'send_time';

    public function getItems(array $options = array())
    {
        $items = [];

        $q = $this->adapter->newQuery('AbandonedCartSchedule');
        $q->where(['removed' => 0]);

        // Get the total count for pagination
        $count = $this->adapter->getCount('AbandonedCartSchedule', $q);
        $this->setTotalCount($count);

        // Set the current page limit and load the object
        $q->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('AbandonedCartSchedule', $q);
        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('from', $this->adapter->lexicon('commerce_abandonedcart.from'), true, true),
            new Column('subject', $this->adapter->lexicon('commerce_abandonedcart.subject'), true, true),
            new Column('send_time', $this->adapter->lexicon('commerce_abandonedcart.send_time'), true, true),
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-message',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.add_message'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('abandonedcarts/schedule/create'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'icon-plus',
            'modal_title' => $this->adapter->lexicon('commerce_abandonedcart.add_message'),
            'position' => 'top'
        ];

        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => (int)$options['limit'],
            'position' => 'bottom',
            'width' => 'two wide',
        ];

        return $toolbar;
    }

    public function prepareItem(\AbandonedCartSchedule $abandonedCartSchedule)
    {
        $item = $abandonedCartSchedule->toArray();

        // Actions
        $item['actions'] = [];
        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('abandonedcarts/schedule/update', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce_abandonedcart.update_message'))
            ->setIcon('icon-edit');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('abandonedcarts/schedule/delete', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce_abandonedcart.delete_message'))
            ->setIcon('icon-trash');

        return $item;
    }
}