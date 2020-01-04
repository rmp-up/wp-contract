<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Session.php
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

namespace RmpUp\Wp\Logging;

use ArrayAccess;

/**
 * Write messages into session
 *
 * WARNING: This will fill up the session real quick when logging low levels.
 * Consider flushing this from time to time
 * or wiping all messages.
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class Variable extends AbstractLogger
{
    const CONTEXT = 'ctxt';
    const MESSAGE = 'msg';
    const PRIORITY = 'prio';
    /**
     * @var int
     */
    private $logLevel;
    /**
     * Reference to the variable where shall be written.
     *
     * @var mixed[]
     */
    private $target;

    /**
     * Session constructor.
     *
     * @param mixed[]|ArrayAccess<string,string[]> $target   Reference that will be filled with an array of messages.
     * @param int                                  $logLevel Log such level and above
     */
    public function __construct(&$target, int $logLevel = LOG_ERR)
    {
        $this->target = &$target;
        $this->logLevel = $logLevel;
    }

    /**
     * @param int      $priority
     * @param string   $message
     * @param string[] $context
     */
    public function __invoke(int $priority, string $message, array $context = [])
    {
        if ($priority > $this->logLevel) {
            return;
        }

        $this->target[] = [
            self::PRIORITY => $priority,
            self::MESSAGE => $message,
            self::CONTEXT => $this->filterContext($context),
        ];
    }
}