<?php
/**
 * Dispatcher file for handling access to a counter
 * via an HTTP request.
 *
 * @package Mistralys\Counters
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * @see ServeViaHTTP
 */

declare(strict_types=1);

use Mistralys\Counters\Counters;
use Mistralys\Counters\ServeViaHTTP;
use function Mistralys\Counters\sendHeader;

if(!file_exists('vendor/autoload.php'))
{
    die('Composer packages must be installed first.');
}

require_once 'vendor/autoload.php';

$appFolder = __DIR__;
$configFile = $appFolder.'/config-local.php';

if(!file_exists($configFile))
{
    sendHeader('500', 'Config file does not exist.'); exit;
}

require_once $configFile;

if(!isset($counters))
{
    sendHeader('500', 'Invalid configuration'); exit;
}

// Create the counters collection
$collection = new Counters($appFolder.'/storage', $counters);
$collection->setUpdateDelay(COUNTERS_UPDATE_DELAY);

// Let the class check the request, and serve
// the counter selected via GET parameters.
$serve = new ServeViaHTTP($collection);
$serve->serve();
