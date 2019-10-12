<?php

namespace PoconoSewVac\AbandonedCart\Services;

use modmore\Commerce\Services\Countries;

/**
 * Class CartConditions
 * This class effectively reimplements modmore\Commerce\Taxes\Condition, but for abandoned carts
 * 
 * @package PoconoSewVac\AbandonedCart\Services
 */
class CartConditions
{
    protected $field = '';
    protected $condition = 'equals';
    protected $value;
    protected $combinator = 'and';

    /**
     * @var \AbandonedCartSchedule
     */
    private $schedule;

    /**
     * @var \AbandonedCartOrder
     */
    private $cart;

    /**
     * CartConditions constructor
     * 
     * @param \AbandonedCartSchedule conditionals
     * @param \AbandonedCartOrder cart instance to check
     */
    public function __construct(\AbandonedCartSchedule $schedule, \comOrder $cart)
    {
        $this->schedule = $schedule;
        $this->cart = $cart;
        $dsl = $this->schedule->get('conditions');

        if (array_key_exists('field', $dsl)) {
            $this->field = trim($dsl['field']);
        }

        if (array_key_exists('condition', $dsl)) {
            $this->condition = trim($dsl['condition']);
        }

        if (array_key_exists('value', $dsl)) {
            $v = $dsl['value'];
            if (!is_numeric($v) && !is_array($v)) {
                $v = trim(strtoupper($v));
            }
            $this->value = $v;
            // Set the value to an array for `in` and `not in` conditions
            if (in_array($this->condition, ['in', 'not in'], true)) {
                $this->value = array_map('trim', explode(',', $this->value));
            }
        }

        if (array_key_exists('combinator', $dsl)) {
            $this->combinator = $dsl['combinator'];
        }
    }

    /**
     * Checks if the order meets the passed in conditions
     * 
     * @return bool
     */
    public function areMet()
    {
        $order = $this->cart->getOrder();

        if (array_key_exists($this->field, $order->_fieldMeta)) {
            $value = $order->get($this->field);
            return $this->checkValue($value);
        }

        $address = $order->getBillingAddress();
        if ($address && array_key_exists($this->field, $address->_fieldMeta)) {
            $value = $address->get($this->field);
            return $this->checkValue($value);
        }

        $expected = $order->getExpectedAddress();
        if ($expected && array_key_exists($this->field, $expected->_fieldMeta)) {
            $value = $expected->get($this->field);
            return $this->checkValue($value);
        }

        return $this->checkValue('');
    }

    private function checkValue($actualValue)
    {
        if (!is_numeric($actualValue) && !is_array($actualValue)) {
            $actualValue = trim(strtoupper($actualValue));
        }
        
        $method = 'check' . implode('', array_map('ucfirst', explode(' ', $this->condition)));
        if (method_exists($this, $method)) {
            return $this->$method($actualValue);
        }

        return null;
    }

    public function checkAlways($value)
    {
        return true;
    }

    public function checkNever($value)
    {
        return false;
    }

    public function checkEquals($value)
    {
        return $this->value === $value;
    }

    public function checkNotEquals($value)
    {
        return !$this->checkEquals($value);
    }

    public function checkIn($value)
    {
        return in_array(strtoupper($value), $this->value, true);
    }

    public function checkNotIn($value)
    {
        return !$this->checkIn($value);
    }

    public function checkEmpty($value)
    {
        if (is_array($value)) {
            return count($value) === 0;
        }

        return $value === '';
    }

    public function checkNotEmpty($value)
    {
        return !$this->checkEmpty($value);
    }

    public function checkGreaterThan($value)
    {
        $value1 = round((float)$value, 5);
        $value2 = round((float)$this->value, 5);
        return $value1 > $value2;
    }

    public function checkLessThan($value)
    {
        $value1 = round((float)$value, 5);
        $value2 = round((float)$this->value, 5);
        return $value1 < $value2;
    }

    private function checkInEuropeanUnion($value)
    {
        return Countries::isInEuropeanUnion($value);
    }

    private function checkNotInEuropeanUnion($value)
    {
        return !$this->checkInEuropeanUnion($value);
    }

    public function getTextSummary()
    {
        $condition = str_replace(' ', '_', $this->condition);
        switch ($this->condition) {
            case 'always':
            case 'never':
                return $this->adapter->lexicon('commerce.condition.' . $condition);
            case 'empty':
            case 'not empty':
            case 'in european union':
            case 'not in european union':
                return '<code>' . $this->encode($this->field) . '</code> ' . $this->adapter->lexicon('commerce.condition.' . $condition);
            default:
                $value = is_array($this->value) ? '[' . implode(', ', $this->value) . ']' : $this->value;
                return '<code>' . $this->encode($this->field) . '</code> ' .
                    $this->adapter->lexicon('commerce.condition.' . $condition) .
                    ' <code>' . $this->encode($value) . '</code>';
        }
    }

    private function encode($value) {
        return htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
}