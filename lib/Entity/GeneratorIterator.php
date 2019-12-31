<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * GeneratorIterator.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package    wp-contract
 * @copyright  2019 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\Wp\Entity;

use ArrayIterator;
use Generator;
use Iterator;

/**
 * GeneratorIterator
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 * @implements Iterator<mixed, mixed>
 */
class GeneratorIterator implements Iterator
{
    /**
     * @var mixed[]
     */
    private $cache = [];
    /**
     * @var \Generator<mixed>
     */
    private $generator;

    /**
     * Extend generator with a rewindable cache
     *
     * @param \Generator<mixed> $generator
     */
    public function __construct($generator)
    {
        $this->generator = $generator;
    }

    /**
     * Return the current element
     *
     * @link  https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $this->assertInit();

        return current($this->cache);
    }

    private function assertInit()
    {
        if ($this->generator->current() && [] === $this->cache) {
            $this->cache[$this->generator->key()] = $this->generator->current();
        }
    }

    /**
     * Return the key of the current element
     *
     * @link  https://php.net/manual/en/iterator.key.php
     * @return string|float|int|bool|null scalar on success, or null on failure.
     */
    public function key()
    {
        $this->assertInit();

        return key($this->cache);
    }

    /**
     * Move forward to next element
     *
     * @link  https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $hasCache = next($this->cache);

        if (false === $hasCache) {
            $this->generator->next();
            if ($this->generator->valid()) {
                $this->cache[$this->generator->key()] = $this->generator->current();
            }
        }
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->cache);
    }

    /**
     * Checks if current position is valid
     *
     * @link  https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->generator->valid()
            || (false !== current($this->cache) && null !== key($this->cache));
    }
}