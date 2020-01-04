<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Psr3Adapter.php
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
 * @since      2019-12-30
 */

declare(strict_types=1);

namespace RmpUp\Wp\Test\Logging;

use Psr\Log\LogLevel;
use RmpUp\Wp\Logging\Psr3Adapter;
use RmpUp\Wp\Logging\TriggerError;
use RmpUp\Wp\Test\LoggingTestCase;

/**
 * Psr3Adapter
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
class Psr3AdapterTest extends LoggingTestCase
{
    /**
     * @dataProvider prioToLevel
     */
    public function testDelegatesPriorities($priority, $expectedLevel)
    {
        $providedLevel = null;
        $providedMessage = null;
        $message = uniqid();

        $logger = new Psr3Adapter(
            function ($invokedLevel, $invokedMessage) use (&$providedLevel, &$providedMessage) {
                $providedLevel = $invokedLevel;
                $providedMessage = $invokedMessage;
            }
        );

        $logger->__invoke($priority, $message);

        static::assertSame($expectedLevel, $providedLevel);
        static::assertSame($message, $providedMessage);
    }

    public function prioToLevel()
    {
        return [
            [LOG_EMERG, LogLevel::EMERGENCY],
            [LOG_ALERT, LogLevel::ALERT],
            [LOG_CRIT, LogLevel::CRITICAL],
            [LOG_ERR, LogLevel::ERROR],
            [LOG_WARNING, LogLevel::WARNING],
            [LOG_NOTICE, LogLevel::NOTICE],
            [LOG_INFO, LogLevel::INFO],
            [LOG_DEBUG, LogLevel::DEBUG],
        ];
    }
}