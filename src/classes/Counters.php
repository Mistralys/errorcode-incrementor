<?php
/**
 * File containing the class {@see \Mistralys\Counters\Counters}.
 *
 * @package Mistralys\Counters
 * @see \Mistralys\Counters\Counters
 */

declare(strict_types=1);

namespace Mistralys\Counters;

/**
 * Counters collection class: allows accessing information on
 * all available named counters.
 *
 * @package Mistralys\Counters
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Counters
{
    /**
     * @var int
     */
    private $delaySeconds = 10;

    /**
     * @var string
     */
    private $storageFolder;

    /**
     * @var array<string,int>
     */
    private $names;

    /**
     * @param string $storageFolder
     * @param array<string,int> $counters
     */
    public function __construct(string $storageFolder, array $counters)
    {
        $this->names = $counters;
        $this->storageFolder = $storageFolder;
    }

    /**
     * Sets the minimum delay after which a counter may be
     * updated again.
     *
     * @param int $seconds
     */
    public function setUpdateDelay(int $seconds) : void
    {
        if($seconds < 1)
        {
            $seconds = 1;
        }

        $this->delaySeconds = $seconds;
    }

    public function getUpdateDelay() : int
    {
        return $this->delaySeconds;
    }

    /**
     * @return string
     */
    public function getStorageFolder(): string
    {
        return $this->storageFolder;
    }

    /**
     * @return Counter[]
     */
    public function getCounters() : array
    {
        $result = array();
        $names = array_keys($this->names);

        foreach ($names as $name)
        {
            $result[] = $this->getByName($name);
        }

        return $result;
    }

    public function counterExists(string $name) : bool
    {
        return isset($this->names[$name]);
    }

    public function getByName(string $name) : Counter
    {
        return new Counter($this, $name);
    }
}
