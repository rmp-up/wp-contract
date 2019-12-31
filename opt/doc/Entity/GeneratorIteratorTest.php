<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * GeneratorIteratorTest.php
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
 * @since      2019-12-31
 */

declare(strict_types=1);

namespace RmpUp\Wp\Test\Entity;

use ArrayObject;
use RmpUp\Wp\Entity\GeneratorIterator;
use RmpUp\Wp\Test\TestCase;

/**
 * GeneratorIteratorTest
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
class GeneratorIteratorTest extends TestCase
{
    /**
     * @var GeneratorIterator
     */
    private $generatorIterator;
    private $someData;

    protected function setUp()
    {
        parent::setUp();

        $this->someData = [
            'one' => 'uno',
            'two' => 3,
            'four' => new ArrayObject([]),
            'five' => 'hundred miles',
            'six' => 6,
            'seven' => 'movie',
            8 => INF,
            'nine' => 'brooklyn',
            0 => 'let us see how that sorts out',
        ];

        $this->generatorIterator = new GeneratorIterator($this->someGenerator());
    }

    private function someGenerator()
    {
        yield from $this->someData;
    }

    public function testCanIterateMultipleTimes()
    {
        static::assertSame($this->someData, iterator_to_array($this->generatorIterator));
        static::assertSame($this->someData, iterator_to_array($this->generatorIterator));
    }

    public function testCanIterateOneTime()
    {
        static::assertSame($this->someData, iterator_to_array($this->generatorIterator));
    }

    public function testCanRewindInBetween()
    {
        $partial = array_slice($this->someData, 0, ceil(count($this->someData) / 2));
        foreach ($partial as $expectedKey => $expectedValue) {
            static::assertSame($expectedKey, $this->generatorIterator->key(), 'key mismatch');
            static::assertSame($expectedValue, $this->generatorIterator->current(), 'value mismatch');

            $this->generatorIterator->next();
        }

        reset($this->generatorIterator);

        // Assert that we can not only refetch the remaining parts
        // but also still have the complete array afterwards.
        static::assertSame($this->someData, iterator_to_array($this->generatorIterator));
        static::assertSame($this->someData, iterator_to_array($this->generatorIterator));
    }
}