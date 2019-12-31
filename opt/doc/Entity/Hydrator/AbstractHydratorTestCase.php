<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractHydratorTest.php
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

use RmpUp\Wp\Entity\HydratorTrait;
use RmpUp\Wp\Test\TestCase;

/**
 * AbstractHydratorTest
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
abstract class AbstractHydratorTestCase extends TestCase
{
    use HydratorTrait;

    private $hydratePrivate;
    protected $hydrateProtected;
    public $hydratePublic;

    protected function assertFieldsAreHydrated($expectedConfig, $object)
    {
        foreach ($expectedConfig as $field => $expectedValue) {
            $getter = 'get' . ucfirst($field);

            static::assertSame($expectedValue, $object->{$getter}(), $field . ' has not been hydrated well');
        }
    }

    /**
     * @return mixed
     */
    abstract public function getHydratePrivate();

    /**
     * @return mixed
     */
    public function getHydrateProtected()
    {
        return $this->hydrateProtected;
    }

    /**
     * @return mixed
     */
    public function getHydratePublic()
    {
        return $this->hydratePublic;
    }

    public function hydratorData()
    {
        return [
            [
                [
                    'hydratePrivate' => uniqid('', true),
                ],
            ], [
                [
                    'hydrateProtected' => uniqid('', true),
                ],
            ], [
                [
                    'hydratePublic' => uniqid('', true),
                ],
            ], [
                [
                    'hydratePrivate' => uniqid('', true),
                    'hydrateProtected' => uniqid('', true),
                    'hydratePublic' => uniqid('', true),
                ]
            ]
        ];
    }
}