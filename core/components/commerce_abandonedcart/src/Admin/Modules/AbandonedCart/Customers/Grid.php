<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers;

use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;

/**
 * Class Grid
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers
 */
class Grid extends GridWidget
{
    public $key = 'abandonedcarts/customers';
    public $defaultSort = 'added_on';

    public function getItems(array $options = array())
    {
        $items = [];

        $q = $this->adapter->newQuery('AbandonedCartUser');
        $q->where(['removed' => 0]);

        // Email filter
        if (array_key_exists('search_by_email', $options) && strlen($options['search_by_email']) > 0) {
            $q->where(['email:LIKE' => '%' . $options['search_by_email'] . '%']);
        }

        // Customer type filter
        if (array_key_exists('customer_type', $options) && strlen($options['customer_type']) > 0) {
            if ((bool) $options['customer_type']) {
                $q->where(['user:>=' => 0]);
            } else {
                $q->where(['user:=' => 0]);
            }
        }

        // Subscribed filter
        if (array_key_exists('subscription_status', $options) && strlen($options['subscription_status']) > 0) {
            if ((bool) $options['subscription_status']) {
                $q->where(['subscribed:=' => 1]);
            } else {
                $q->where(['subscribed:=' => 0]);
            }
        }

        // Get the total count for pagination
        $count = $this->adapter->getCount('AbandonedCartUser', $q);
        $this->setTotalCount($count);

        // Set the current page limit and load the object
        $q->limit($options['limit'], $options['start']);
        $collection = $this->adapter->getCollection('AbandonedCartUser', $q);
        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('user', $this->adapter->lexicon('commerce_abandonedcart.customer'), false, true),
            new Column('email', $this->adapter->lexicon('commerce_abandonedcart.email'), false),
            new Column('subscribed', $this->adapter->lexicon('commerce_abandonedcart.subscribed'), true, true),
        ];
    }

    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'search_by_email',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.search_by_email'),
            'type' => 'textfield',
            'value' => array_key_exists('search_by_email', $options) ? (int)$options['search_by_email'] : '',
            'position' => 'top',
            'width' => 'six wide'
        ];

        $toolbar[] = [
            'name' => 'customer_type',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.customer_type'),
            'type' => 'select',
            'value' => array_key_exists('status', $options) ? (int)$options['status'] : '',
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.guest'),
                    'value' => 0,
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.registered'),
                    'value' => 1,
                ],
            ],
            'position' => 'top',
            'width' => 'two wide',
        ];

        $toolbar[] = [
            'name' => 'subscription_status',
            'title' => $this->adapter->lexicon('commerce_abandonedcart.subscription_status'),
            'type' => 'select',
            'value' => array_key_exists('subscription_status', $options) ? (int)$options['subscription_status'] : '',
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.unsubscribed'),
                    'value' => 0,
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.subscribed'),
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

    public function prepareItem(\AbandonedCartUser $abandonedCartUser)
    {
        $item = $abandonedCartUser->toArray();
        $userId = $abandonedCartUser->get('user');

        // User column
        if ($abandonedCartUser->get('user') > 0) {
            $editUserLink = $this->adapter->getOption('manager_url') . '?a=security/user/update&id=' . $userId;
            $item['user'] = '<a href="' . $editUserLink . '">' . $userId . '</a>';
        } else {
            $item['user'] = $this->adapter->lexicon('commerce_abandonedcart.guest');
        }

        // Subscription column
        if ($abandonedCartUser->isSubscribed()) {
            $item['subscribed'] = '<i class="icon icon-check"></i> ' . $this->adapter->lexicon('commerce_abandonedcart.subscribed');
        } else {
            $item['subscribed'] = '<i class="icon icon-times"></i> ' . $this->adapter->lexicon('commerce_abandonedcart.unsubscribed');
        }

        // Actions
        $item['actions'] = [];
        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('abandonedcarts/customers/update', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce_abandonedcart.update_customer'))
            ->setIcon('icon-edit');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('abandonedcarts/customers/delete', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce_abandonedcart.delete_customer'))
            ->setIcon('icon-trash');

        return $item;
    }
}