<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ErrorLog.php
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
 * @since      2020-01-02
 */

declare(strict_types=1);

namespace RmpUp\Wp\Logging;

/**
 * Forward to error_log using PSR-3 prefixes for messages
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class ErrorLog extends Psr3Adapter
{
    const CONTEXT_DEPTH = 4;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var int
     */
    private $messageType;

    /**
     * @var int[]
     * @see https://www.php.net/manual/de/errorfunc.constants.php
     */
    protected $prioToLevel = [
        LOG_EMERG => E_ERROR,
        LOG_ALERT => E_ERROR,
        LOG_CRIT => E_ERROR,
        LOG_ERR => E_ERROR,
        LOG_WARNING => E_WARNING,
        LOG_NOTICE => E_NOTICE,
        LOG_INFO => E_NOTICE,
        LOG_DEBUG => E_STRICT,
    ];

    /**
     * ErrorLog constructor.
     *
     * @param int           $messageType
     * @param string        $destination
     * @param string[]|null $prefixMapping
     * @param int[]|null    $priorityToErrorLevel
     */
    public function __construct(int $messageType = 0, string $destination = '', array $prefixMapping = null, array $priorityToErrorLevel = null)
    {
        parent::__construct([$this, 'log'], $prefixMapping);

        if (null !== $priorityToErrorLevel) {
            $this->prioToLevel = $priorityToErrorLevel;
        }

        $this->messageType = $messageType;
        $this->destination = $destination;
    }

    /**
     *
     * @param int|null $prefix
     * @param string   $message
     * @param mixed[]  $context
     */
    protected function log($prefix, $message, $context = [])
    {
        error_log($prefix . ': ' . $message . $this->createSuffix($context));
    }

    public function __invoke($priority, $message, $context = [])
    {
        if (false === $this->isEnabled($priority)) {
            return;
        }

        parent::__invoke($priority, $message, $context);
    }

    /**
     * Generate suffix out of context
     *
     * @param mixed[] $context
     *
     * @return string
     */
    protected function createSuffix(array $context): string
    {
        if (!$context) {
            return '';
        }

        $assoc = array_filter($context, 'is_string', ARRAY_FILTER_USE_KEY);

        if (!$assoc) {
            return '';
        }

        return ' ' . json_encode(
                array_merge(
                    array_filter($assoc, 'is_scalar'),
                    array_map('get_class', array_filter($assoc, 'is_object'))
                )
            );
    }

    /**
     * Check if this syslog-priority is allowed to be logged
     *
     * @param int $priority
     *
     * @return bool
     */
    protected function isEnabled(int $priority): bool
    {
        return array_key_exists($priority, $this->prioToLevel)
            && 1 <= (error_reporting() & $this->prioToLevel[$priority]);
    }
}