<?php
namespace PoconoSewVac\AbandonedCart\Admin\Widgets\Form;

use modmore\Commerce\Admin\Widgets\Form\Field;

/**
 * Class MessageScheduleField
 * @package PoconoSewVac\AbandonedCart\Admin\Widgets\Form
 */
class MessageScheduleField extends Field
{
    public function isValidValue($value)
    {
        return is_string($value) || is_array($value);
    }

    public function setValue($value)
    {
        if (is_string($value) && $array = json_decode($value, true)) {
            $value = $array;
        }
        if (is_array($value)) {
            foreach ($value as $k => $item) {
                if (empty($item['send_time'])) {
                    unset($value[$k]);
                }
            }
        }
        $this->value = $value;
        return $this;
    }

    public function getHTML()
    {
        return $this->commerce->twig->render('abandonedcart/admin/widgets/fields/message-schedule.twig', ['field' => $this]);
    }

    public function getValueAsArray()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return $value;
        }
        if ($array = json_decode($value, true)) {
            return $array;
        }
        return [];
    }
}