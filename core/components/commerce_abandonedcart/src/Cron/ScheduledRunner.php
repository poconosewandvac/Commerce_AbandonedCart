<?php

namespace PoconoSewVac\AbandonedCart\Cron;

use PoconoSewVac\AbandonedCart\Repositories\CartRepository;
use PoconoSewVac\AbandonedCart\Services\Conditions;

/**
 * Class Runner
 * @package PoconoSewVac\AbandonedCart\Cron
 */
final class ScheduledRunner implements Runnable
{
    /**
     * @var \Commerce
     */
    protected $commerce;

    /**
     * @var \modmore\Commerce\Adapter\AdapterInterface|\modmore\Commerce\Adapter\Revolution
     */
    protected $adapter;

    /**
     * Runner constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
    }

    private function getSchedule()
    {
        $schedule = $this->adapter->getCollection('AbandonedCartSchedule', [
            'removed' => false
        ]);

        return $schedule ?: [];
    }

    /**
     * Removes the unsubscribed users from the pending carts
     * 
     * @param \AbandonedCartOrder[] $carts
     */
    private function removeUnsubscribed($carts)
    {
        return array_map(function($cart) {
            if ($user->isSubscribed()) {
                return true;
            } else {
                $cart->remove();
                return false;
            }
        }, $carts);
    }

    public function run()
    {
        $cartRepository = new CartRepository($this->commerce);
        $schedule = $this->getSchedule();

        if (empty($schedule)) {
            $this->adapter->log(3, '[AbandonedCart] No messages have been scheduled during cron run. Schedule a message in the Commerce dashboard -> Abandoned Carts -> Schedule.');
            return;
        }

        /** @var \AbandonedCartOrder[] $carts */
        $carts = $cartRepository->getPending();
        $carts = $this->removeUnsubscribed($carts);

        foreach ($carts as $cart) {
            $order = $cart->getOrder();

            foreach ($schedule as $scheduledMessage) {
                if ($scheduledMessage->conditionsMetForCart($cart)) {
                    if ($scheduledMessage->send($cart)) {
                        $this->adapter->log(4, '[AbandonedCart] Sent message ' . $schedule->get('id') . ' for order with ID ' . $order->get('id'));
                    } else {
                        $this->adapter->log(1, '[AbandonedCart] Failed to send message ' . $schedule->get('id') . ' for order with ID ' . $order->get('id'));
                    }

                    // Prevent multiple messages from being sent at once
                    continue 2;
                }
            }
        }
    }
}