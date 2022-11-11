<?php

namespace PoconoSewVac\AbandonedCart\Reports;

use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Reports\BaseReport;
use modmore\Commerce\Reports\Data\Header;
use modmore\Commerce\Reports\Data\Row;

/**
 * Class Users
 * @package PoconoSewVac\AbandonedCart\Reports
 */
class Users extends BaseReport
{
    public function getName()
    {
        return $this->adapter->lexicon('commerce_abandonedcart.user_report');
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_abandonedcart.user_report_desc');
    }

    public function getGroup()
    {
        return 'orders';
    }

    public function getOptions()
    {
        $fields = [];

        $fields[] = new SelectField($this->commerce, [
            'name' => 'subscription_status',
            'label' => $this->adapter->lexicon('commerce_abandonedcart.subscription_status'),
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.all'),
                    'value' => -1,
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.unsubscribed'),
                    'value' => 0
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.subscribed'),
                    'value' => 1
                ]
            ],
            'value' => -1,
        ]);

        return $fields;
    }

    public function getDataHeaders()
    {
        $headers = [];

        $headers[] = new Header('id', 'id', true);
        $headers[] = new Header('email', 'email', true);
        $headers[] = new Header('subscribed', 'subscribed', true);
        $headers[] = new Header('fullname', 'fullname', true);
        $headers[] = new Header('firstname', 'firstname', true);
        $headers[] = new Header('lastname', 'lastname', true);
        $headers[] = new Header('phone', 'phone', true);
        $headers[] = new Header('cellphone', 'cellphone', true);
        $headers[] = new Header('mobilephone', 'mobilephone', true);
        $headers[] = new Header('dob', 'dob', true);
        $headers[] = new Header('gender', 'gender', true);
        $headers[] = new Header('address', 'address', true);
        $headers[] = new Header('country', 'country', true);
        $headers[] = new Header('city', 'city', true);
        $headers[] = new Header('state', 'state', true);
        $headers[] = new Header('zip', 'zip', true);
        $headers[] = new Header('fax', 'fax', true);
        $headers[] = new Header('photo', 'photo', true);
        $headers[] = new Header('website', 'website', true);

        return $headers;
    }

    public function getDataRows()
    {
        $rows = [];

        $subscriptionStatus = $this->getOption('subscription_status');

        $c = $this->adapter->newQuery('AbandonedCartUser');
        $c->select('AbandonedCartUser.*, modUserProfile.*');
        $c->leftJoin('modUserProfile', 'modUserProfile', ['AbandonedCartUser.user = modUserProfile.internalKey']);
        $c->where(['removed' => 0]);

        // Subscription status filter
        if ((int) $subscriptionStatus !== -1) { // all
            $c->where(['subscribed' => (bool) $subscriptionStatus]);
        }

        foreach ($this->adapter->getIterator('AbandonedCartUser', $c) as $user) {
            $rows[] = new Row($user->toArray());
        }

        return $rows;
    }

    public function getAvailableCharts()
    {
        return [];
    }
}
