<?php

namespace PoconoSewVac\AbandonedCart\Modules;

use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\TopNavMenu as TopNavMenuEvent;
use modmore\Commerce\Events\Reports;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use PoconoSewVac\AbandonedCart\Repositories\CartRepository;
use PoconoSewVac\AbandonedCart\Repositories\UserRepository;
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

    /** @var CartRepository $cartRepository */
    protected $cartRepository;

    /** @var UserRepository $userRepository */
    protected $userRepository;

    /**
     * AbandonedCart constructor.
     * @param \Commerce $commerce
     */
    public function __construct(\Commerce $commerce)
    {
        parent::__construct($commerce);

        $this->cartRepository = new CartRepository($commerce);
        $this->userRepository = new UserRepository($commerce);
    }

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

        // Add template path to twig
        /** @var ChainLoader $loader */
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'loadPages']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'loadMenuItem']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_REPORTS_GET_REPORTS, [$this, 'addReports']);
        $dispatcher->addListener(\Commerce::EVENT_ORDER_ADDRESS_ADDED, [$this, 'addAbandonedCart']);
        $dispatcher->addListener(\Commerce::EVENT_ORDER_MESSAGE_PLACEHOLDERS, [$this, 'addMessagePlaceholders']);

        // Determine which event to use for converted on
        $markConvertedOn = constant($this->getConfig('converted_on_method'));
        if ($markConvertedOn === \Commerce::EVENT_CHECKOUT_AFTER_STEP) {
            $dispatcher->addListener(\Commerce::EVENT_CHECKOUT_AFTER_STEP, [$this, 'convertAbandonedCartThankYou']);
        } else if ($markConvertedOn === \Commerce::EVENT_STATE_CART_TO_PROCESSING) {
            $dispatcher->addListener(\Commerce::EVENT_STATE_CART_TO_PROCESSING, [$this, 'convertAbandonedCartToProcessing']);
        }
    }

    /**
     * Adds reports into Commerce
     *
     * @param Reports $event
     */
    public function addReports(Reports $event)
    {
        $event->addReport(new \PoconoSewVac\AbandonedCart\Reports\Users($this->commerce));
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
        if (!$order) {
            return;
        }

        // Get the abandoned cart user
        $user = $this->userRepository->getByOrder($order);
        if (!$user) {
            $user = $this->userRepository->addFromOrder($order);
        }

        // Make sure user is not unsubscribed
        if ($user && !$user->isSubscribed()) {
            return;
        }

        // Make sure abandoned cart does not already exist for this order
        /** @var \AbandonedCartOrder $abandonedCartOrder */
        $abandonedCartOrder = $this->cartRepository->getByOrder($order);
        if ($abandonedCartOrder) return;

        $this->cartRepository->addFromOrder($order, $user);
        $order->log($this->adapter->lexicon('commerce_abandonedcart.added_abandonedcart', [
            'id' => $order->get('id'),
        ]), false);
    }

    /**
     * Remove the abandoned cart by marking it as converted
     *
     * @param \comOrder $event
     * @return void
     */
    public function convertAbandonedCart(\comOrder $order)
    {
        /** @var \AbandonedCartOrder $abandonedCartOrder */
        $abandonedCartOrder = $this->cartRepository->getByOrder($order);
        if (!$abandonedCartOrder) {
            return;
        }

        // Mark as converted, since the order is completed
        $abandonedCartOrder->markConverted();
        $abandonedCartOrder->save();

        $order->log($this->adapter->lexicon('commerce_abandonedcart.converted_abandonedcart', [
            'id' => $abandonedCartOrder->get('id'),
        ]), false);
    }

    /**
     * Remove abandoned cart on payment received
     *
     * @param \modmore\Commerce\Events\Payment $event
     * @return void
     */
    public function convertAbandonedCartToProcessing(\modmore\Commerce\Events\OrderState $state)
    {
        $this->convertAbandonedCart($state->getOrder());
    }

    /**
     * Remove abandoned cart on thank-you step
     *
     * @param \modmore\Commerce\Events\Checkout $event
     * @return void
     */
    public function convertAbandonedCartThankYou(\modmore\Commerce\Events\Checkout $event)
    {
        $step = $event->getStep();
        if (!($step instanceof ThankYou)) {
            return;
        }

        $this->convertAbandonedCart($event->getOrder());
    }

    /**
     * Set placeholder URL in Abandoned Cart email for restoring the cart
     * @param \modmore\Commerce\Events\MessagePlaceholders $event
     */
    public function addMessagePlaceholders(\modmore\Commerce\Events\MessagePlaceholders $event)
    {
        $baseUrl = rtrim($this->adapter->getOption('site_url'), '/') . $this->adapter->getOption('assets_url');
        $identifier = $event->getOrder()->get('secret');

        $event->setPlaceholder('restore_link', $baseUrl . 'components/commerce_abandonedcart/cart.php?secret=' . urlencode($identifier));
    }

    public function loadPages(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();

        $generator->addPage('abandonedcarts', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Overview::class);
        $generator->addPage('abandonedcarts/delete', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Delete::class);

        $generator->addPage('abandonedcarts/customers', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers\Overview::class);
        $generator->addPage('abandonedcarts/customers/update', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers\Update::class);
        $generator->addPage('abandonedcarts/customers/delete', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Customers\Delete::class);

        $generator->addPage('abandonedcarts/schedule', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule\Overview::class);
        $generator->addPage('abandonedcarts/schedule/create', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule\Create::class);
        $generator->addPage('abandonedcarts/schedule/update', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule\Update::class);
        $generator->addPage('abandonedcarts/schedule/delete', \PoconoSewVac\AbandonedCart\Admin\Modules\AbandonedCart\Schedule\Delete::class);
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
                    'name' => $this->adapter->lexicon('commerce_abandonedcart.carts'),
                    'key' => 'abandonedcarts',
                    'icon' => 'icon icon-shopping-cart',
                    'link' => $this->adapter->makeAdminUrl('abandonedcarts'),
                ],
                [
                    'name' => $this->adapter->lexicon('commerce_abandonedcart.customers'),
                    'key' => 'abandonedcarts/customers',
                    'icon' => 'icon icon-user',
                    'link' => $this->adapter->makeAdminUrl('abandonedcarts/customers')
                ],
                [
                    'name' => $this->adapter->lexicon('commerce_abandonedcart.schedule'),
                    'key' => 'abandonedcarts/schedule',
                    'icon' => 'icon icon-calendar',
                    'link' => $this->adapter->makeAdminUrl('abandonedcarts/schedule')
                ],
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
                    'label' => $this->adapter->lexicon('commerce_abandonedcart.converted_on_method_state_cart_to_processing'),
                    'value' => '\Commerce::EVENT_STATE_CART_TO_PROCESSING',
                ]
            ],
            'default' => '\Commerce::EVENT_STATE_CART_TO_PROCESSING',
        ]);

        return $fields;
    }

    /**
     * @param $array
     * @param $values
     * @param $offset
     * @return array
     */
    private function insertInArray($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }
}
