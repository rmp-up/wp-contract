<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCli.php
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
 * Forward syslog-like logging-messages to WP_CLI output
 *
 * @package RmpUp\Wp\Logging
 */
class WpCli extends AbstractLogger
{
    /**
     * @var string[]
     */
    protected $priorityMapping = [
        LOG_EMERG => 'error',
        LOG_ALERT => 'error',
        LOG_CRIT => 'error',
        LOG_ERR => 'error',
        LOG_WARNING => 'warning',
        LOG_NOTICE => 'log',
        LOG_INFO => 'debug',
        LOG_DEBUG => 'debug',
    ];

    /**
     * Write syslog-like message to CLI.
     *
     * @param int $priority Syslog-like priority.
     * @param string $message Text that shall be logged.
     */
    public function __invoke($priority, $message)
    {
        $method = $this->translate($priority);

        if (null === $method) {
            return;
        }

        $callback = '\\WP_CLI::' . $method;

        if (is_callable($callback)) {
            $callback($message, false);
        }
    }
}