<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LoggingTestCase.php
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

namespace RmpUp\Wp\Test;

use phpmock\phpunit\PHPMock;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\Wp\Logging\AbstractLogger;

/**
 * Logging
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class LoggingTestCase extends TestCase
{
    use FilterAssertions;
    use PHPMock;

    protected $logger;
}

class TranslationReflection extends AbstractLogger
{
    public function __invoke($priority, $message, $context = [])
    {
        return $this->translate($priority);
    }
}