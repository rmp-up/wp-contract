<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TriggerError.php
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
 * Forwarding log messages to trigger_error
 *
 * @package RmpUp\Wp\Logging
 */
class TriggerError extends AbstractLogger
{
    /**
     * @var int[]
     * @see https://www.php.net/manual/de/errorfunc.constants.php
     */
    protected $priorityMapping = [
        LOG_EMERG => E_USER_ERROR,
        LOG_ALERT => E_USER_ERROR,
        LOG_CRIT => E_USER_ERROR,
        LOG_ERR => E_USER_ERROR,
        LOG_WARNING => E_USER_WARNING,
        LOG_NOTICE => E_USER_NOTICE,
        LOG_INFO => E_USER_NOTICE,
        // LOG_DEBUG   => E_USER_NOTICE,
    ];

    /**
     * @param int    $priority Syslog-like priority of the message.
     * @param string $message  Text to throw via trigger_error.
     */
    public function __invoke(int $priority, $message)
    {
        $errorType = $this->translate($priority);

        if (null === $errorType) {
            // Could not translate priority to error level so we refuse to trigger an error.
            return;
        }

        \trigger_error($message, $errorType);
    }
}