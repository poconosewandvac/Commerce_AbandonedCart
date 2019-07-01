<?php

namespace PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart;

use modmore\Commerce\Admin\Widgets\GridWidget;
use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;

/**
 * Class Grid
 * @package PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart
 */
class Grid extends GridWidget
{
    public $key = 'abandonedcarts';
    public $defaultSort = 'added_on';

    public function getItems(array $options = array())
    {
        $items = [];

        $q = $this->adapter->newQuery('AbandonedCartOrder');
        $q->where(['AbandonedCartOrder.removed' => 0]);
        $q->leftJoin('comOrder', 'Order', ['Order.id = AbandonedCartOrder.order']);
        $q->leftJoin('comOrderAddress', 'Address', ['Address.order = Order.id']);

        if (array_key_exists('customer_type', $options) && strlen($options['customer_type']) > 0) {
            if ((bool) $options['customer_type']) {
                $q->where(['AbandonedCartOrder.user:>=' => 0]);
            } else {
                $q->where(['AbandonedCartOrder.user:=' => 0]);
            }
        }

        if (array_key_exists('converted', $options) && strlen($options['converted']) > 0) {
            $q->where([
                'AbandonedCartOrder.user' => (bool) $options['converted'],
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
            new Column('user', $this->adapter->lexicon('commerce_abandonedcart.customer'), false, true),
            new Column('order', $this->adapter->lexicon('commerce.order'), false, true),
            new Column('msg_count', $this->adapter->lexicon('commerce_abandonedcart.msg_count'), true),
            new Column('added_on', $this->adapter->lexicon('commerce_abandonedcart.added_on'), true),
            new Column('converted', $this->adapter->lexicon('commerce_abandonedcart.converted'), true),
            new Column('converted_on', $this->adapter->lexicon('commerce_abandonedcart.converted_on'), true),
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
        /** @var \comOrder $order */
        $order = $abandonedCart->getOrder();

        $item = $order->toArray();
        $item['abandoned_cart'] = $abandonedCart->toArray();
        $itemId = $order->get('id');

        $detailUrl = $this->adapter->makeAdminUrl('order', ['order' => $itemId]);
        $item['detail_url'] = $detailUrl;

        $address = $order->getShippingAddress();
        if ($address) {
            $item['shipping_address'] = $address->toArray();
        }

        $item['shipping_method'] = [];
        $item['shipments'] = [];

        /** @var \comOrderShipment[] $shipments */
        $shipments = $this->adapter->getCollection('comOrderShipment', ['order' => $order->get('id')]);
        $item['shipping_method'] = [];
        foreach ($shipments as $shipment) {
            $method = $shipment->getShippingMethod(false);
            $item['shipping_method'][] = $method ? $method->get('name') : '#' . $shipment->get('method');

            $sa = $shipment->toArray();
            $sa['method'] = $method ? $method->toArray() : false;
            $item['shipments'][] = $sa;
        }

        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod) {
            $item['shipping_method'][] = $shippingMethod->get('name');
        }
        $item['shipping_method'] = implode(', ', $item['shipping_method']);

        // Transactions
        $item['payment_method'] = [];
        $transactions = $order->getTransactions();
        if (count($transactions) !== 0) {
            $transaction = reset($transactions);
            $paymentMethod = $transaction->getMethod();
            $item['payment_method'][] = $paymentMethod ? $paymentMethod->get('name') : '#' . $transaction->get('method');
        }
        $item['payment_method'] = implode(', ', $item['payment_method']);

        // Order items
        $item['items'] = [];
        foreach ($order->getItems() as $orderItem) {
            $oia = $orderItem->toArray();
            if ($product = $orderItem->getProduct()) {
                $oia['product'] = $product->toArray();
            }
            $item['items'][] = $oia;
        }

        $item['actions'] = [];
        if ($this->adapter->hasPermission('commerce_order')) {
            $deleteUrl = $this->adapter->makeAdminUrl('abandonedcarts/delete', ['id' => $abandonedCart->get('id')]);
            $item['actions'][] = (new Action())
                ->setUrl($deleteUrl)
                ->setTitle($this->adapter->lexicon('commerce_abandonedcart.delete_abandoned_cart'))
                ->setIcon('icon-trash');

            $item['actions'][] = (new Action())
                ->setUrl($detailUrl)
                ->setIcon('zoom')
                ->setModalTitle($this->adapter->lexicon('commerce.order.quick_view_details'));
            $item['actions'][] = (new Action())
                ->setUrl($detailUrl)
                ->setTitle($this->adapter->lexicon('commerce.order.view_details'))
                ->setModal(false);
        }

        if ($this->adapter->hasPermission('commerce_order_messages_send')) {
            $item['actions'][] = (new Action())
                ->setUrl($this->adapter->makeAdminUrl('order/messages/create', ['order' => $order->get('id'), 'class_key' => 'comOrderEmailMessage']))
                ->setTitle($this->adapter->lexicon('commerce.create_message'));
        }

        return $item;
    }

    public function render(array $phs)
    {
        return $this->commerce->twig->render('abandonedcart/admin/widgets/abandoned-cart-grid.twig', $phs);
    }
}