<?php

// Check if the script is being run from the CLI
// This should not be able to be run from the web
if (php_sapi_name() !== 'cli') {
    http_response_code(406); // not acceptable
    
    echo json_encode([
        'success' => false,
        'message' => 'Cannot be run from web.',
    ]);

    die();
}

// Load MODX
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH.'config/' .MODX_CONFIG_KEY. '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

// Load Commerce
$commercePath = $modx->getOption('commerce.core_path', null, $modx->getOption('core_path') . 'components/commerce/') . 'model/commerce/';
/** @var \Commerce $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $commercePath, ['mode' => $modx->getOption('commerce.mode')]);

// Load AbandonedCart
$corePath = $modx->getOption('commerce_abandonedcart.core_path', null, $modx->getOption('core_path') . 'components/commerce_abandonedcart');
require_once $corePath . 'model/commerce_abandonedcart/abandonedcart.class.php';
require_once $corePath . 'vendor/autoload.php';

$scheduledRunner = new \PoconoSewVac\AbandonedCart\Cron\ScheduledRunner($commerce);
$scheduledRunner->run();