<?php
/**
 * File containing the {@see \Mistralys\Counters\Counter} class.
 *
 * @package Mistralys\Counters
 * @see \Mistralys\Counters\Counter
 */

declare(strict_types=1);

namespace Mistralys\Counters;

/**
 * Counter class used to handle a single named counter
 * in the list. Handles accessing the counter's number
 * and to increment it, saving the file to disk.
 *
 * @package Mistralys\Counters
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Counter
{
    const ERROR_CANNOT_SAVE_COUNTER = 11201;
    const ERROR_CANNOT_LOAD_COUNTER = 11202;
    const ERROR_BELOW_MINIMUM_DELAY = 11203;

    /**
     * @var Counters
     */
    private $collection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $storageFile;

    /**
     * @var int
     */
    private $number = 0;

    /**
     * @var bool
     */
    private $loaded = false;

    public function __construct(Counters $collection, string $name)
    {
        $this->collection = $collection;
        $this->name = $name;
        $this->storageFile = $collection->getStorageFolder().'/'.md5($name).'.txt';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Counters
     */
    public function getCollection(): Counters
    {
        return $this->collection;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getNumber() : int
    {
        $this->load();

        return $this->number;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function increment() : int
    {
        $this->load();

        $this->number++;

        $this->save();

        return $this->number;
    }

    /**
     * @throws Exception
     */
    private function save() : void
    {
        $this->requireValidDelay();

        if(file_put_contents($this->storageFile, $this->number))
        {
            return;
        }

        throw new Exception(
            'Cannot save counter',
            self::ERROR_CANNOT_SAVE_COUNTER
        );
    }

    /**
     * @throws Exception
     */
    private function load() : void
    {
        if($this->loaded)
        {
            return;
        }

        $this->loaded = true;

        if(!file_exists($this->storageFile))
        {
            return;
        }

        $content = file_get_contents($this->storageFile);

        if($content !== false)
        {
            $this->number = intval($content);
            return;
        }

        throw new Exception(
            'Cannot load counter',
            self::ERROR_CANNOT_LOAD_COUNTER
        );
    }

    /**
     * Checks if the counter may be accessed again
     * after having been updated.
     *
     * @throws Exception
     */
    private function requireValidDelay() : void
    {
        $modified = filemtime($this->storageFile);
        $min = $modified + $this->collection->getUpdateDelay();

        if(time() >= $min)
        {
            return;
        }

        throw new Exception(
            'Accessed before minimum counter delay',
            self::ERROR_BELOW_MINIMUM_DELAY
        );
    }
}
