<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MessageBus.php
 *
 * LICENSE: This source file is created by the company around Mr. M. Pretzlaw
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

namespace RmpUp\Wp\Bus;

/**
 * MessageBus
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
trait MessageBusTrait
{
    /**
     * Requesting information
     *
     * @param object $statement Simple representation of the statement.
     */
    public function query($statement)
    {
        return $this->delegateToWp('apply_filters', $statement);
    }

    /**
     * Forward bus event to WordPress
     *
     * @param string            $callback
     * @param array<int, mixed> $arguments
     *
     * @return mixed|null
     */
    protected function delegateToWp($callback, ...$arguments)
    {
        array_unshift($arguments, get_class(reset($arguments)));

        if (!is_callable($callback)) {
            return null;
        }

        try {
            return $callback(...$arguments);
        } catch (StopPropagation $e) {
            return $e->result();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Delegate exception handling via command bus
     *
     * Wraps the exception in a status-container.
     * All parents classes and interfaces will run through the bus.
     *
     * Example: An \DomainException does ...
     *
     * - `MessageBus::handle( \DomainException::class, new HandleException( $e ) )`
     * - Its direct parent `MessageBus::handle( \RuntimeException::class, ...`
     * - Its direct parent `...::handle( \RuntimeException::class, ... )`
     * - Higher parents like `... \Exception::class ...`
     * - The interface `... \Throwable::class ...`
     *
     * @param \Throwable $e
     *
     * @throws \Throwable
     */
    protected function handleException(\Throwable $e)
    {
        // Ask others to handle this exception ...
        $descision = new HandleException($e);

        $selfAndAncestors = array_merge(
            [get_class($e)],
            class_parents(get_class($e), false),
            class_implements($e)
        );

        foreach ($selfAndAncestors as $parent) {
            \do_action($parent, $descision);
        }

        if (false === $descision->is(HandleException::SUPPRESS)) {
            throw $e;
        }
    }

    /**
     * @param object $command Task to execute
     *
     * @return mixed|null
     */
    public function handle($command)
    {
        return $this->delegateToWp('do_action', $command);
    }
}