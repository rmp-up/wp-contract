<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MessageBusInterface.php
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
 * MessageBusInterface
 *
 * @copyright  2019 Pretzlaw (https://rmp-up.de)
 */
interface MessageBusInterface
{
    /**
     * @param object $query
     *
     * @return mixed
     */
    public function query($query);

    /**
     * @param object $query
     *
     * @return mixed
     */
    public function handle($query);
}