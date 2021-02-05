<?php
/**
 * File containing the class {@see \Mistralys\Counters\ServeViaHTTP}.
 *
 * @package Mistralys\Counters
 * @see \Mistralys\Counters\ServeViaHTTP
 */

declare(strict_types=1);

namespace Mistralys\Counters;

/**
 * Used to serve a counter's current number, as well as
 * increasing it, via HTTP requests.
 *
 * Accepts the following GET parameters:
 *
 * - pw: The password [required]
 * - counter: The name of the counter to select
 * - increment (yes/no): Whether to increment the counter
 *
 * @package Mistralys\Counters
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ServeViaHTTP
{
    /**
     * @var Counters
     */
    private $counters;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Counter
     */
    private $counter;

    public function __construct(Counters $counters)
    {
        $this->counters = $counters;
    }

    /**
     * Serves the counter number, incrementing it as needed,
     * then exits the script.
     */
    public function serve() : void
    {
        $this->validateRequest();

        $this->counter = $this->counters->getByName($this->name);

        $this->increment();

        try
        {
            header('Content-Type:text/plain');
            echo $this->counter->getNumber();
        }
        catch (Exception $e)
        {
            sendHeader('500', 'Error #'.$e->getCode().': '.$e->getMessage());
        }

        exit;
    }

    /**
     * Handles incrementing the counter, if the request variable
     * has been set.
     */
    private function increment() : void
    {
        if(!isset($_REQUEST['increment']) || $_REQUEST['increment'] !== 'yes')
        {
            return;
        }

        try
        {
            $this->counter->increment();
        }
        catch (Exception $e)
        {
            if($e->getCode() === Counter::ERROR_BELOW_MINIMUM_DELAY)
            {
                sendHeader('403', 'Minimum update delay not respected.'); exit;
            }

            sendHeader('500', 'Error #'.$e->getCode().': '.$e->getMessage()); exit;
        }
    }

    /**
     * Ensures that the request is valid, by verifying the
     * password, and loading the specified counter.
     */
    private function validateRequest() : void
    {
        if(!isset($_REQUEST['pw']) || $_REQUEST['pw'] !== COUNTERS_PASSWORD)
        {
            sendHeader('401', 'Unauthorized'); exit;
        }

        if(!isset($_REQUEST['counter']))
        {
            sendHeader('400', 'Counter not specified'); exit;
        }

        if(!$this->counters->counterExists($_REQUEST['counter']))
        {
            sendHeader('404', 'Counter not found'); exit;
        }

        $this->name = $_REQUEST['counter'];
    }
}
