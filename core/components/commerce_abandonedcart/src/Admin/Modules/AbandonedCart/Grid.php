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

        $q = $this->adapter->newQuery('AbandonedCartOrder');
        $q->leftJoin('comOrder', 'Order', ['Order.id = AbandonedCartOrder.order']);
        $q->leftJoin('comOrderAddress', 'Address', ['Address.order = Order.id']);

        if (array_key_exists('converted', $options) && strlen($options['converted']) > 0) {
            $q->where([
                'AbandonedCartOrder.converted' => (bool) $options['converted'],
            ]);
        }

        if (array_key_exists('search_by_customer', $options) && strlen($options['search_by_customer']) > 0) {
            $addressSearch = $options['search_by_customer'];
            $q->where([
                'Address.fullname:LIKE' => "%{$addressSearch}%",
                'OR:Address.firstname:LIKE' => "%{$addressSearch}%",
                'OR:Address.lastname:LIKE' => "%{$addressSearch}%",
                'OR:Address.company:LIKE' => "%{$addressSearch}%",
                'OR:Address.address1:LIKE' => "%{$addressSearch}%",
                'OR:Address.address2:LIKE' => "%{$addressSearch}%",
                'OR:Address.address3:LIKE' => "%{$addressSearch}%",
                'OR:Address.zip:LIKE' => "%{$addressSearch}%",
                'OR:Address.city:LIKE' => "%{$addressSearch}%",
                'OR:Address.state:LIKE' => "%{$addressSearch}%",
            ]);
        }

        // Get the total count for pagination
        $count = $this->adapter->getCount('AbandonedCartOrder', $q);
        $this->setTotalCount($count);

        // Set the current page limit and load the object
        $q->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('AbandonedCartOrder', $q);
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