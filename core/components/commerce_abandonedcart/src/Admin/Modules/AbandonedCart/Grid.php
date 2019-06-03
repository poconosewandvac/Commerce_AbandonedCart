<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;

use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce\Admin\Util\Action;

class Grid extends GridWidget
{
    public $key = 'abandonedcarts';
    public $defaultSort = 'added_on';

    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery('AbandonedCartOrder');
        if (array_key_exists('search_by_customer', $options) && strlen($options['search_by_customer']) > 0) {
            $c->where([
                'name:LIKE' => '%' . $options['search_by_customer'] . '%'
            ]);
        }

        // Get the total count for pagination
        $count = $this->adapter->getCount('AbandonedCartOrder', $c);
        $this->setTotalCount($count);

        // Set the current page limit and load the object
        $c->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('AbandonedCartOrder', $c);
        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            [
                'name' => 'user',
                'title' => $this->adapter->lexicon('commerce_abandonedcart.customer'),
                'sortable' => true,
            ],
            [
                'name' => 'order',
                'title' => $this->adapter->lexicon('commerce.order'),
                'sortable' => true,
            ],
            [
                'name' => 'msg_count',
                'title' => $this->adapter->lexicon('commerce_abandonedcart.msg_count'),
                'sortable' => true,
            ],
            [
                'name' => 'added_on',
                'title' => $this->adapter->lexicon('commerce_abandonedcart.added_on'),
                'sortable' => true,
            ],
            [
                'name' => 'converted',
                'title' => $this->adapter->lexicon('commerce_abandonedcart.converted'),
                'sortable' => true,
            ],
            [
                'name' => 'converted_on',
                'title' => $this->adapter->lexicon('commerce_abandonedcart.converted_on'),
                'sortable' => true,
            ]
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'search_by_customer',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.search_by_customer'),
            'type' => 'textfield',
            'value' => array_key_exists('search_by_customer', $options) ? (int)$options['search_by_customer'] : '',
            'position' => 'top',
            'width' => 'six wide'
        ];

        $toolbar[] = [
            'name' => 'converted',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.converted'),
            'type' => 'select',
            'value' => array_key_exists('status', $options) ? (int)$options['status'] : '',
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.not_converted'),
                    'value' => 0,
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.converted'),
                    'value' => 1,
                ],
            ],
            'position' => 'top',
            'width' => 'three wide',
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

    public function prepareItem(\AbandonedCartOrder $abandonedCart)
    {
        $item = $abandonedCart->toArray('', false, true);

        $item['actions'] = [];

        $deleteUrl = $this->adapter->makeAdminUrl('abandonedcarts/delete', ['id' => $item['id']]);
        $item['actions'][] = (new Action())
            ->setUrl($deleteUrl)
            ->setTitle($this->adapter->lexicon('commerce.delete'))
            ->setIcon('icon-trash');
        return $item;
    }
}