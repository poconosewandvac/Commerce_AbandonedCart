<?php

namespace PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation;

use \modmore\Commerce\Admin\Widgets\Form\Validation\Rule;

/**
 * Class MessageScheduleTime
 * @package PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation
 */
class MessageScheduleTime extends Rule
{
    public function isValid($value)
    {
        foreach ($value as $v) {
            $timeStr = $v['send_time'];
            if (!empty($timeStr) && !strtotime($timeStr)) {
                return 'commerce_abandonedcart.send_time_invalid';
            }
        }

        return true;
    }
}