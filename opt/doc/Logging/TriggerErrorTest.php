<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TriggerErrorTest.php
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

namespace RmpUp\Wp\Test;

use RmpUp\Wp\Logging\TriggerError;

/**
 * TriggerErrorTest
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
class TriggerErrorTest extends TestCase
{
    /**
     * @dataProvider prioToErrorLevel
     */
    public function testTriggerUserLevelErrors($priority, $expectedErrorLevel)
    {
        $logger = new TriggerError();

        $providedLevel = null;
        $providedMessage = null;

        set_error_handler(
            function ($level, $message) use (&$providedLevel, &$providedMessage) {
                $providedMessage = $message;
                $providedLevel = $level;
            }
        );

        $message = uniqid();

        $logger->__invoke($priority, $message);


        restore_error_handler();

        static::assertSame($expectedErrorLevel, $providedLevel);
        static::assertSame($message, $providedMessage);
    }

    public function prioToErrorLevel()
    {
        return [
            [LOG_EMERG, E_USER_ERROR],
            [LOG_ALERT, E_USER_ERROR],
            [LOG_CRIT, E_USER_ERROR],
            [LOG_ERR, E_USER_ERROR],
            [LOG_WARNING, E_USER_WARNING],
            [LOG_NOTICE, E_USER_NOTICE],
            [LOG_INFO, E_USER_NOTICE],
        ];
    }
}