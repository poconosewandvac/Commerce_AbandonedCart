<?php

namespace PoconoSewVac\AbandonedCart\Services;

/**
 * Class Conditions
 * @package PoconoSewVac\AbandonedCart\Services
 */
class Conditions
{
    private $conditions;

    public function __construct($conditions)
    {
        $this->conditions = $conditions;
    }
}