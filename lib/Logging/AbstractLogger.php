<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractLogger.php
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
 */

declare(strict_types=1);

namespace RmpUp\Wp\Logging;

/**
 * Generic mapping from syslog level to something else
 *
 * @package RmpUp\Wp\Logging
 */
abstract class AbstractLogger
{
    /**
     * Mapping from syslog (int) to mixed target
     *
     * @var array<int, mixed>
     */
    protected $priorityMapping = [];

    /**
     * Abstract logger adapter
     *
     * @param mixed[] $priorityMapping Possibility to override predefined mapping.
     */
    public function __construct(array $priorityMapping = null)
    {
        if (null !== $priorityMapping) {
            $this->priorityMapping = $priorityMapping;
        }
    }

    /**
     * Turn syslog-priority to targeted logging severity
     *
     * @param int $priority
     *
     * @see https://www.php.net/manual/de/function.syslog.php
     */
    protected function translate(int $priority)
    {
        if (!array_key_exists($priority, $this->priorityMapping)) {
            return null;
        }

        return $this->priorityMapping[$priority];
    }
}