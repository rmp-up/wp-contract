<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HandleExceptionTest.php
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
 * @since      2020-01-01
 */

declare(strict_types=1);

namespace RmpUp\Wp\Test\Bus\ExceptionHandling;

use Exception;
use OverflowException;
use RmpUp\Wp\Bus\HandleException;
use RmpUp\Wp\Test\Bus\AbstractMessageBusTestCase;
use RmpUp\Wp\Test\TestCase;
use RuntimeException;
use Throwable;

/**
 * HandleExceptionTest
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class HandleExceptionTest extends AbstractMessageBusTestCase
{
    private $parentsAndInterfaces = [
        RuntimeException::class,
        Exception::class,
        Throwable::class,
    ];

    private function assertConsecutiveFilter($exceptionOrder)
    {
        return function (HandleException $command) use ($exceptionOrder) {
            static::assertInstanceOf(OverflowException::class, $command->getException());

            $next = array_shift($exceptionOrder);
            $mock = $this->mockFilter($next)->expects($this->once());

            if ($exceptionOrder) {
                $mock->willReturnCallback($this->assertConsecutiveFilter($exceptionOrder));
            }
        };
    }

    public function getParentsAndInterfaces()
    {
        return [
            [RuntimeException::class],
            [Exception::class],
            [Throwable::class],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        // Throw exception when sending $this in.
        $this->mockFilter(HandleExceptionTest::class)
            ->expects($this->any())
            ->willThrowException(new OverflowException());
    }

    public function testCanSuppressExceptions()
    {
        $this->mockFilter(OverflowException::class)->expects($this->once())->willReturnCallback(
            function (HandleException $command) {
                $command->markAsSuppresed();
            }
        );

        $this->messageBus->handle($this);
    }

    public function testDelegatesAlsoParentsAndInterfaces()
    {
        $this->expectException(OverflowException::class);

        $this->mockFilter(OverflowException::class)
            ->expects($this->once())
            ->willReturnCallback($this->assertConsecutiveFilter(
                $this->parentsAndInterfaces
            ));

        $this->messageBus->handle($this);
    }

    public function testExceptionDelegatesToHandleExceptionCommand()
    {
        $this->expectException(OverflowException::class);

        $this->mockFilter(OverflowException::class)->expects($this->once());

        $this->messageBus->handle($this);
    }

    /**
     * @dataProvider getParentsAndInterfaces
     */
    public function testParentsAndInterfaceHandlerCanSuppressExceptions($parentOrInterface)
    {
        $this->mockFilter($parentOrInterface)->expects($this->once())->willReturnCallback(
            function (HandleException $command) {
                $command->markAsSuppresed();
            }
        );

        $this->messageBus->handle($this);
    }

    public function testSuppressingCanBeRevoked()
    {
        $this->mockFilter(OverflowException::class)->expects($this->once())->willReturnCallback(
            function (HandleException $command) {
                static::assertFalse($command->is(HandleException::SUPPRESS));

                $command->markAsSuppresed();

                static::assertTrue($command->is(HandleException::SUPPRESS));
            }
        );

        $this->mockFilter(RuntimeException::class)->expects($this->once())->willReturnCallback(
            function (HandleException $command) {
                static::assertTrue($command->is(HandleException::SUPPRESS));
                static::assertFalse($command->is(HandleException::THROW_EXCEPTION));

                $command->markToThrow();

                static::assertFalse($command->is(HandleException::SUPPRESS));
                static::assertTrue($command->is(HandleException::THROW_EXCEPTION));
            }
        );

        $this->expectException(OverflowException::class);
        $this->messageBus->handle($this);
    }

    public function testCanBeMarkedAsHandled()
    {
        $cmd = new HandleException(new \Exception());

        $cmd->markAsHandled();

        static::assertTrue($cmd->is(HandleException::HANDLED));
    }
}