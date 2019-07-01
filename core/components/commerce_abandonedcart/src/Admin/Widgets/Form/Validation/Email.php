<?php

namespace PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation;

use \modmore\Commerce\Admin\Widgets\Form\Validation\Rule;

/**
 * Class Email
 * @package PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation
 */
class Email extends Rule
{
    /**
     * @param $value
     * @return bool|string
     */
    public function isValid($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return 'commerce_abandonedcart.email_invalid';
    }
}