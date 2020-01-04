<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VariableTest.php
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

use ArrayObject;
use RmpUp\Wp\Logging\Reference;
use RmpUp\Wp\Logging\Variable;
use RmpUp\Wp\Test\LoggingTestCase;

/**
 * Writing in variables / references
 *
 * This is mightier than it looks.
 * Writing messages in some variable can act like a buffer.
 * Writing it in an object that implements ArrayAccess
 * gives even more control over the buffer.
 * Putting all information in `$_SESSION` is helpful for carrying messages around.
 * Simply pass any variable to this
 * and it will be filled up with all the information.
 *
 * Messages will be added as an array like this:
 *
 * ```php
 * <?php
 *
 * $targetVariable = [];
 * $logger = new Variable($targetVariable);
 *
 * $logger->__invoke(LOG_ERR, 'Pi = 3', ['pi' => M_PI]);
 *
 * // Target variable now contains one message:
 * $targetVariable = [
 *   [
 *     Variable::PRIORITY => LOG_ERR,
 *     Variable::MESSAGE => 'Pi = 3',
 *     Variable::CONTEXT => ['pi' => M_PI],
 *   ]
 * ];
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 * @property Variable $logger
 */
class VariableTest extends LoggingTestCase
{
    protected function assertMessageWritten($expectedPriority, $expectedMessage, $expectedContext = [], $variable = null)
    {
        static::assertNotEmpty($variable);
        static::assertArraySubset(
            [
                [
                    Variable::PRIORITY => $expectedPriority,
                    Variable::MESSAGE => $expectedMessage,
                    Variable::CONTEXT => $expectedContext,
                ]
            ],
            $variable
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $_SESSION[__CLASS__] = [];

        $this->logger = new Variable($_SESSION[__CLASS__], LOG_ERR);

        static::assertEmpty($_SESSION[__CLASS__]);
    }

    public function testAllowsArrayAccessInterface()
    {
        $array = new ArrayObject();
        $logger = new Variable($array);

        $logger(LOG_ERR, 'get off the island!');

        static::assertSame(
            [
                [
                    Variable::PRIORITY => LOG_ERR,
                    Variable::MESSAGE => 'get off the island!',
                    Variable::CONTEXT => [],
                ]
            ],
            (array) $array
        );
    }

    public function testDoesNotWriteIgnoredErrorLevels()
    {
        $this->logger->__invoke(LOG_DEBUG, 'debug');
        $this->logger->__invoke(LOG_INFO, 'info');
        $this->logger->__invoke(LOG_NOTICE, 'notice');
        $this->logger->__invoke(LOG_WARNING, 'warning');
        $this->logger->__invoke(LOG_ERR, 'err', ['a' => 'b']);

        static::assertCount(1, $_SESSION[__CLASS__]);
        static::assertSame(
            [
                [
                    Variable::PRIORITY => LOG_ERR,
                    Variable::MESSAGE => 'err',
                    Variable::CONTEXT => ['a' => 'b']
                ]
            ],
            $_SESSION[__CLASS__]
        );
    }

    /**
     * Example: Writing into session
     *
     * If you want to present success or error messages between two requests
     * then it may become handy storing them in the session.
     * But only if you take care that this stays as lightweight as possible:
     *
     * ```php
     * <?php
     *
     * use RmpUp\Wp\Logging\Variable;
     *
     * $_SESSION['myOwnSpace'] = [];
     * $logger = new Variable($_SESSION['myOwnSpace']);
     *
     * // $someLoggingBus->register($logger);
     * ```
     */
    public function testWritesIntoSession()
    {
        $this->logger->__invoke(LOG_EMERG, 'yay');

        $this->assertMessageWritten(LOG_EMERG, 'yay', [], $_SESSION[__CLASS__]);
    }

    /**
     * Example: Writing to $GLOBAL
     *
     * In case you want to keep the log up for just this runtime but available to all
     * you could write it in the superglobal $GLOBAL like this:
     *
     * ```php
     * <?php
     *
     * use RmpUp\Wp\Logging\Variable;
     *
     * global $myLog;
     *
     * $logger = new Variable($myLog);
     * ```
     *
     * Log messages can now be found in `$myLog` and `$GLOBAL['myLog']`.
     */
    public function testWritesToGlobal()
    {
        global $writesToGlobal;

        $logger = new Variable($writesToGlobal);
        $logger->__invoke(LOG_ALERT, 'global test');

        $this->assertMessageWritten(LOG_ALERT, 'global test', [], $GLOBALS['writesToGlobal']);

        unset($writesToGlobal);
    }
}