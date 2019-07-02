<?php

namespace PoconoSewVac\AbandonedCart\Cron;

use PoconoSewVac\AbandonedCart\Repositories\CartRepository;

/**
 * Class Runner
 * @package PoconoSewVac\AbandonedCart\Cron
 */
final class Runner
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

    public function run()
    {
        $cartRepository = new CartRepository($this->commerce);

        $pending = $cartRepository->getPending();
        foreach ($pending as $order) {
        }
    }
}