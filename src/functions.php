<?php
/**
 * Global functions for the counters.
 *
 * @package Mistralys\Counters
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

declare(strict_types=1);

namespace Mistralys\Counters;

/**
 * Sends an HTTP status header.
 *
 * @param string $status
 * @param string $message
 */
function sendHeader(string $status, string $message) : void
{
    header('HTTP/1.1 '.$status.' '.$message);
}
