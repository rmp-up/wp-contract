<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Psr3Adapter.php
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

use Psr\Log\LogLevel;

/**
 * Forward syslog messages to PSR-3 logger
 *
 * @package RmpUp\Wp\Logging
 */
class Psr3Adapter extends AbstractLogger
{
    /**
     * @var string[]
     */
    protected $priorityMapping = [
        LOG_EMERG => LogLevel::EMERGENCY,
        LOG_ALERT => LogLevel::ALERT,
        LOG_CRIT => LogLevel::CRITICAL,
        LOG_ERR => LogLevel::ERROR,
        LOG_WARNING => LogLevel::WARNING,
        LOG_NOTICE => LogLevel::NOTICE,
        LOG_INFO => LogLevel::INFO,
        LOG_DEBUG => LogLevel::DEBUG,
    ];
    /**
     * @var callable
     */
    private $callback;

    /**
     * Psr3Translator constructor.
     *
     * @param callable $callback       PSR-3 compatible invoke / callback.
     * @param string[] $prioityToLevel Possibility to override syslog to PSR-3 error level mapping.
     */
    public function __construct($callback, $prioityToLevel = null)
    {
        $this->callback = $callback;
        parent::__construct($prioityToLevel);
    }

    /**
     * Forward syslog-like message to PSR-3 logger
     *
     * @param int     $priority
     * @param string  $message
     * @param mixed[] $context
     */
    public function __invoke($priority, $message, $context = [])
    {
        $logger = $this->callback;
        $level = $this->translate($priority);

        if (null !== $level) {
            $logger($level, $message, $context);
        }
    }
}