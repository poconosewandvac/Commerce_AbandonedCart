<?php

namespace PoconoSewVac\AbandonedCart\Modules;

use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu as TopNavMenuEvent;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce\Admin\Widgets\Form\DescriptionField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use PoconoSewVac\AbandonedCart\Admin\Widgets\Form\MessageScheduleField;
use PoconoSewVac\AbandonedCart\Admin\Widgets\Form\Validation\MessageScheduleTime;
use Symfony\Component\EventDispatcher\EventDispatcher;
use modmore\Commerce\Frontend\Steps\ThankYou;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

/**
 * Class AbandonedCart
 * @package PoconoSewVac\AbandonedCart\Modules
 */
class AbandonedCart extends BaseModule {

    /** @var \AbandonedCart $abandonedCart */
    protected $abandonedCart;

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_abandonedcart:default');
        return $this->adapter->lexicon('commerce_abandonedcart');
    }

    public function getAuthor()
    {
        return 'Tony Klapatch';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_abandonedcart.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_abandonedcart:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_abandonedcart', $path);

        // Load AbandonedCart
        $this->adapter->loadClass('commerce_abandonedcart.AbandonedCartModel', $path, true, true);
        $this->abandonedCart = new \AbandonedCartModel($this->commerce);

        // Add template path to twig
        /** @var ChainLoader $loader */
        $root = dirname(__DIR__, 2);
        $loader = $this->commerce->twig->getLoader();
        $loader->addLoader(new FilesystemLoader($root . '/templates/'));

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'loadPages']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'loadMenuItem']);
        $dispatcher->addListener(\Commerce::EVENT_ORDER_ADDRESS_ADDED, [$this, 'addAbandonedCart']);

        // Determine which event to use for converted on
        $markConvertedOn = constant($this->getConfig('converted_on_method'));
        if ($markConvertedOn === \Commerce::EVENT_CHECKOUT_AFTER_STEP) {
            $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_AFTER_STEP, [$this, 'removeAbandonedCartThankYou']);
        } else if ($markConvertedOn === \Commerce::EVENT_ORDER_PAYMENT_RECEIVED) {
            $dispatcher->addListener(\Commerce::EVENT_ORDER_PAYMENT_RECEIVED, [$this, 'removeAbandonedCartPaymentReceived']);
        }
    }

    /**
     * Adds the abandoned cart as soon as the customer enters an address
     *
     * @param \modmore\Commerce\Events\Address $event
     * @return void
     */
    public function addAbandonedCart(\modmore\Commerce\Events\Address $event)
    {
        $order = $event->getOrder();
        
        // Get the abandoned cart user
        $user = $this->abandonedCart->getUser($order);
        if (!$user) {
            $this->abandonedCart->addUser($order);
        }

        // Make sure abandoned cart does not already exist for this order
        $abandonedCartOrder = $this->abandonedCart->getOrder($order);
        if ($abandonedCartOrder) return;

        $this->abandonedCart->addOrder($order);
    }

    /**
     * Remove abandoned cart on payment received
     *
     * @param \modmore\Commerce\Events\Payment $event
     * @return void
     */
    protected function removeAbandonedCartPaymentReceived(\modmore\Commerce\Events\Payment $event)
    {
        $this->removeAbandonedCart($event->getOrder());
    }

    /**
     * Remove abandoned cart on thank-you step
     *
     * @param \modmore\Commerce\Events\Checkout $event
     * @return void
     */
    protected function removeAbandonedCartThankYou(\modmore\Commerce\Events\Checkout $event)
    {
        $step = $event->getStep();
        if (!($step instanceof ThankYou)) {
            return;
        }

        $this->removeAbandonedCart($event->getOrder());
    }

    /**
     * Remove the abandoned cart by marking it as converted
     *
     * @param \comOrder $event
     * @return void
     */
    public function removeAbandonedCart(\comOrder $order)
    {
        $abandonedCartOrder = $this->abandonedCart->getOrder($order);
        if ($abandonedCartOrder) {
            // Mark as converted, since the order is completed
            $abandonedCartOrder->markConverted();
            $abandonedCartOrder->save();
        }
    }

    public function loadPages(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();
        $generator->addPage('abandonedcarts', '\PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Overview');
        $generator->addPage('abandonedcarts/delete', '\PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Delete');

        // $generator->addPage('abandonedcarts/customers', '\modmore\Commerce\Admin\Modules\Customers\Guests');
    }

    public function loadMenuItem(TopNavMenuEvent $event)
    {
        $items = $event->getItems();

        $items = $this->insertInArray($items, ['abandonedcarts' => [
            'name' => $this->adapter->lexicon('commerce_abandonedcarts'),
            'key' => 'abandonedcarts',
            'icon' => 'icon icon-shopping-cart',
            'link' => $this->adapter->makeAdminUrl('abandonedcarts'),
            'submenu' => [
                [
                    'name' => $this->adapter->lexicon('commerce_abandonedcarts'),
                    'key' => 'abandonedcarts',
                    'icon' => 'icon shopping-cart',
                    'link' => $this->adapter->makeAdminUrl('abandonedcarts')
                ],
                /*[
                    'name' => $this->adapter->lexicon('commerce_abandonedcarts.customers'),
                    'key' => 'abandonedcarts/customers',
                    'icon' => 'icon icon-user',
                    'link' => $this->adapter->makeAdminUrl('abandonedcarts/customers')
                ]*/
            ]
        ]], 3);

        $event->setItems($items);
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        $fields[] = new SelectField($this->commerce, [
            'label' => $this->adapter->lexicon('commerce_abandonedcart.converted_on_method'),
            'description' => $this->adapter->lexicon('commerce_abandonedcart.converted_on_method_desc'),
            'name' => 'properties[converted_on_method]',
            'value' => $module->getProperty('converted_on_method'),
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.converted_on_method_thank_you'),
                    'value' => '\Commerce::EVENT_CHECKOUT_AFTER_STEP',
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.converted_on_method_payment_received'),
                    'value' => '\Commerce::EVENT_ORDER_PAYMENT_RECEIVED',
                ]
            ],
            'default' => '\Commerce::EVENT_ORDER_PAYMENT_RECEIVED',
        ]);

        $fields[] = new MessageScheduleField($this->commerce, [
            'label' => $this->adapter->lexicon('commerce_abandonedcart.schedule'),
            'description' => $this->adapter->lexicon('commerce_abandonedcart.schedule_desc'),
            'name' => 'properties[schedule]',
            'value' => $module->getProperty('schedule'),
            'validation' => [ new MessageScheduleTime() ],
        ]);

        return $fields;
    }

    private function insertInArray($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }
}
