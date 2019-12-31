<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MessageBusTest.php
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

use ArrayObject;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RecursiveTreeIterator;
use RmpUp\Wp\Bus\MessageBus;
use RmpUp\Wp\Bus\StopPropagation;
use RmpUp\Wp\Test\Bus\AbstractMessageBusTestCase;

/**
 * MessageBusTest
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
class MessageBusTest extends AbstractMessageBusTestCase
{
    public function testSendQueryToApplyFilter()
    {
        $this->mockFilter(ArrayObject::class)->expects($this->once())->willReturn(13);
        $this->mockFilter(ArrayObject::class)->expects($this->once())->willReturn(42);

        static::assertSame(42, $this->messageBus->query(new ArrayObject()));
    }

    public function testStopPropagationInQuery()
    {
        $this->mockFilter(ArrayObject::class)->expects($this->once())->willThrowException(new StopPropagation(1337));
        $this->mockFilter(ArrayObject::class)->expects($this->never())->willReturn(5);

        static::assertSame(1337, $this->messageBus->query(new ArrayObject()));
    }

    public function testSendCommandToDoAction()
    {
        $this->mockFilter(ArrayObject::class)->expects($this->once());
        $this->mockFilter(ArrayObject::class)->expects($this->once());
        $this->mockFilter(RecursiveTreeIterator::class)->expects($this->never());

        $this->messageBus->handle(new ArrayObject());
    }

    public function testStopPropagationForCommand()
    {
        $this->mockFilter(\ArrayObject::class)->expects($this->atLeastOnce())->willThrowException(new StopPropagation());
        $this->mockFilter(\ArrayObject::class)->expects($this->never())->willReturn(42);

        $this->messageBus->handle(new ArrayObject([]));
    }
}