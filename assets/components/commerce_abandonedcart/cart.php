<?php

/**
 * Load a previous cart into a user's current session
 * This is required to always restore the customer's cart
 */

// Load MODX
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH.'config/' .MODX_CONFIG_KEY. '.inc.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

// Load Commerce
$commercePath = $modx->getOption('commerce.core_path', null, $modx->getOption('core_path') . 'components/commerce/') . 'model/commerce/';
/** @var \Commerce $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $commercePath, ['mode' => $modx->getOption('commerce.mode')]);

// Load AbandonedCart
$corePath = $modx->getOption('commerce_abandonedcart.core_path', null, $modx->getOption('core_path') . 'components/commerce_abandonedcart');
require_once $corePath . 'vendor/autoload.php';

$previousOrder = \PoconoSewVac\AbandonedCart\Frontend\PreviousOrder::fromSecret($commerce, $_GET['secret']);

if (!$previousOrder) {
    $modx->sendErrorPage();
}

$previousOrder->restore();

$cartUrl = $modx->makeUrl($modx->getOption('commerce.cart_resource'));
$modx->sendRedirect($cartUrl);