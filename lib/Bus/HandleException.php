<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ExceptionState.php
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

namespace RmpUp\Wp\Bus;

/**
 * Container to carry and let decide what to do with this exception
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class HandleException
{
    const HANDLED = 1;
    const SUPPRESS = 4;
    const THROW_EXCEPTION = 2;
    const UNHANDLED = 0;

    /**
     * The exception that occured
     *
     * @var \Throwable
     */
    private $exception;

    /**
     * Current state of the exception
     *
     * @var int
     */
    private $state;

    /**
     * New exception to take care of
     *
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
        $this->reset();
    }

    /**
     * Get the
     *
     * @return \Throwable
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Current state of the exception
     *
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * Check if the exception contains a state.
     *
     * @param int $state
     *
     * @return bool
     */
    public function is(int $state): bool
    {
        return (bool) ($this->getState() & $state);
    }

    public function markAsHandled()
    {
        $this->state |= static::HANDLED;
    }

    /**
     * Still throw exception
     *
     * Hint: This is not not named "throw" alone because it would be a reserved
     * word in PHP. We don't do that here.
     */
    public function markToThrow()
    {
        $this->state ^= static::SUPPRESS;
        $this->state |= static::THROW_EXCEPTION;
    }

    /**
     * Mark that this exception shall be suppressed
     */
    public function markAsSuppresed()
    {
        $this->state = static::HANDLED | static::SUPPRESS;
    }

    /**
     * Mark as unhandled and that is shall be thrown
     */
    public function reset()
    {
        $this->state = static::UNHANDLED | static::THROW_EXCEPTION;
    }
}