<?php

namespace PoconoSewVac\AbandonedCart\Frontend;

/**
 * Class MessageList
 * @package PoconoSewVac\AbandonedCart\Frontend
 */
class MessageList
{
    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var string $key
     */
    protected $key;

    /**
     * MessageList constructor.
     * @param $key
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @param string $message
     */
    public function add($message): void
    {
        $this->data[] = $message;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->data = [];
    }
}
