<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractMessageBusTestCase.php
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

namespace RmpUp\Wp\Test\Bus;

use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\Wp\Bus\MessageBus;
use RmpUp\Wp\Test\TestCase;

/**
 * AbstractMessageBusTestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class AbstractMessageBusTestCase extends TestCase
{
    use FilterAssertions;

    /**
     * @var MessageBus
     */
    protected $messageBus;

    protected function setUp()
    {
        parent::setUp();

        $this->messageBus = new MessageBus();
    }
}