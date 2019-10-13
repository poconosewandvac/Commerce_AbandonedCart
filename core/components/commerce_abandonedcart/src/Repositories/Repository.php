<?php

namespace PoconoSewVac\AbandonedCart\Repositories;

abstract class Repository
{
    /** @var \Commerce $commerce */
    protected $commerce;

    /** @var \modmore\Commerce\Adapter\AdapterInterface $adapter */
    protected $adapter;

    /**
     * UserRepository constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
    }
}