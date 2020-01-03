<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ErrorLogTest.php
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
use Error;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\StringContains;
use RmpUp\Wp\Logging\ErrorLog;
use RmpUp\Wp\Test\LoggingTestCase;

/**
 * Forwarding to error_log()
 *
 * Having [error_log](https://php.net/error_log) handling messages
 * can be done using the `\RmpUp\Wp\Logging\ErrorLog` adapter.
 * Add this in your event bus to forward syslog-like logging:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\Wp\Logging\ErrorLog;
 *
 * $someEventBus->register( 'your_log_queue_name', new ErrorLog() );
 * ```
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 * @property ErrorLog $logger
 */
class ErrorLogTest extends LoggingTestCase
{
    private $errorReportingLevel;
    private $prioToLevel = [
        LOG_EMERG => E_ERROR,
        LOG_ALERT => E_ERROR,
        LOG_CRIT => E_ERROR,
        LOG_ERR => E_ERROR,
        LOG_WARNING => E_WARNING,
        LOG_NOTICE => E_NOTICE,
        LOG_INFO => E_NOTICE,
        LOG_DEBUG => E_STRICT,
    ];
    private $receivedMessage;

    public function contextToJson()
    {
        return [
            [
                [
                    1 => 'drop',
                    'user' => 2,
                    'request' => new ArrayObject(),
                    'nested' => ['drop'],
                ],
                '{"user":2,"request":"ArrayObject"}'
            ]
        ];
    }

    public function getPrioAndLevel()
    {
        foreach ($this->prioToLevel as $prio => $level) {
            yield [$prio, $level];
        }
    }

    public function getPsr3Mapping()
    {
        return $this->psr3Test->prioToLevel();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function mockErrorLog(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getFunctionMock('RmpUp\\Wp\\Logging', 'error_log');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->errorReportingLevel = error_reporting();
        $this->logger = new ErrorLog();
    }

    protected function tearDown()
    {
        restore_error_handler();
        error_reporting($this->errorReportingLevel);

        parent::tearDown();
    }

    /**
     * To make those logs more readable
     * the PSR-3 error level will be prefixed to each message:
     *
     * - LOG_EMERG messages are prefixed with 'emergency'
     * - LOG_ALERT: 'alert'
     * - LOG_CRIT: 'critical'
     * - LOG_ERR: 'error'
     * - LOG_WARNING: 'warning'
     * - LOG_NOTICE: 'notice'
     * - LOG_INFO: 'info'
     * - LOG_DEBUG: 'debug'
     *
     * @dataProvider \RmpUp\Wp\Test\Logging\Psr3AdapterTest::prioToLevel()
     */
    public function testAddsPrefix($priority, $psr3Prefix)
    {
        $this->mockErrorLog()
            ->expects($this->once())
            ->with($psr3Prefix . ': foo');

        $this->logger->__invoke($priority, 'foo');
    }

    /**
     * Context
     *
     * The context will be added as a JSON-like suffix.
     * But it will only add the named fields of the context
     * and only those that are scalar types.
     *
     * Example for a context:
     *
     * ```php
     * [
     *  1 => 'numeric keys are dropped',
     *  'user' => 2,
     *  'request' => new ArrayObject(['complex data here']), // will be changed
     *  'nested' => [
     *    'this will be' => 'removed too'
     *  ],
     * ]
     * ```
     *
     * After dropping all numeric keys only scalar values remain to keep the
     * log small and clean of too much clutter.
     * Remaining information turns into JSON:
     *
     * ```json
     * {
     *   "user": 2,
     *   "request": "ArrayObject"
     * }
     * ```
     *
     * The name of objects remain to point out that there is more information
     * to look up.
     *
     * @dataProvider contextToJson
     */
    public function testContextAsSuffix(array $context, string $suffix)
    {
        $this->mockErrorLog()->expects($this->once())->with('emergency: foo ' . $suffix);

        $this->logger->__invoke(LOG_EMERG, 'foo', $context);
    }

    /**
     * @dataProvider getPrioAndLevel
     */
    public function testRespectsErrorReportingLevel(int $priority, int $errorLevel)
    {
        $errorLog = $this->mockErrorLog();

        // Next one should appear
        error_reporting($errorLevel);
        $errorLog->expects($this->once());
        $this->logger->__invoke($priority, 'exact prio');

        // Next one should not because error_reporting forbids it
        error_reporting($errorLevel - 1);
        $errorLog->expects($this->never());
        $this->logger->__invoke($priority, 'less');
    }

    public function testNoSuffixWhenNoContextIsLeftOrGiven()
    {
        $this->mockErrorLog()->expects($this->exactly(2))->with('foo: bar');

        $logger = new ErrorLog(0, '', [LOG_DEBUG => 'foo']);

        $logger->__invoke(LOG_DEBUG, 'bar'); // no context
        $logger->__invoke(LOG_DEBUG, 'bar', [1 => 'will be removed']); // filtered out
    }
}