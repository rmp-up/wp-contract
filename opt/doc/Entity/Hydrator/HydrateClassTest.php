<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HydrateClassTest.php
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

namespace RmpUp\Wp\Test\Entity\Hydrator;

use __PHP_Incomplete_Class;
use ArrayObject;
use ReflectionProperty;
use RmpUp\Wp\Entity\HydratorTrait;
use RmpUp\Wp\Test\TestCase;
use RuntimeException;

class Falsy {
    public function __wakeup()
    {
        throw new \DomainException('');
    }

}

/**
 * HydrateClassTest
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
class HydrateClassTest extends AbstractHydratorTestCase
{
    private $hydratePrivate;

    /**
     * @return mixed
     */
    public function getHydratePrivate()
    {
        return $this->hydratePrivate;
    }

    public function testBypassesConstructor()
    {
        /** @var static $copy */
        $copy = $this->hydrateClass(__CLASS__, []);

        static::assertNull($copy->dataName());

        static::assertSame('', $copy->getDataSetAsString());
    }

    /**
     * @param $config
     *
     * @dataProvider hydratorData
     */
    public function testHydratesFields($config)
    {
        $copy = $this->hydrateClass(__CLASS__, $config);

        $this->assertFieldsAreHydrated($config, $copy);
    }
}