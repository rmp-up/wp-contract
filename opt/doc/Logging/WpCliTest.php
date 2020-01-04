<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCliTest.php
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
 * @copyright  2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @since      2020-01-03
 */

declare(strict_types=1);

namespace RmpUp\Wp\Test\Logging;

use RmpUp\Wp\Logging\WpCli;
use RmpUp\Wp\Test\LoggingTestCase;

/**
 * WpCliTest
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class WpCliTest extends LoggingTestCase
{
    private $reflect;

    public function getPrioToMethod()
    {
        return [
            [LOG_EMERG, 'error'],
            [LOG_ALERT, 'error'],
            [LOG_CRIT, 'error'],
            [LOG_ERR, 'error'],
            [LOG_WARNING, 'warning'],
            [LOG_NOTICE, 'info'],
            [LOG_INFO, 'info'],
            [LOG_DEBUG, 'debug'],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->reflect = new class {
            public $recent = ['name' => null, 'message' => null];

            public function __call($name, $arguments)
            {
                $this->recent['name'] = $name;
                $this->recent['message'] = current($arguments);
            }
        };

        \WP_CLI::set_logger($this->reflect);

        $this->logger = new WpCli();
    }

    /**
     * @dataProvider getPrioToMethod
     */
    public function testDelegatesToWpCliLogger($prio, $method)
    {
        $this->logger->__invoke($prio, 'foo');

        static::assertSame($method, $this->reflect->recent['name']);
    }

    public function testDoesNotDelegateInvalidPriorities()
    {
        $this->logger->__invoke(PHP_INT_MAX, 'foo');

        static::assertNull($this->reflect->recent['name']);
    }
}