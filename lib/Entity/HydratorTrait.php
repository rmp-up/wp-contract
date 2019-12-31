<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Hydrator.php
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
 * @package   wp-contract
 * @copyright 2019 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\Wp\Entity;

/**
 * Hydrator
 *
 * @copyright 2019 Pretzlaw (https://rmp-up.de)
 */
trait HydratorTrait
{
    private $default;
    protected $hydrateClassCache = [];
    protected $hydrateClosure;

    /**
     * Create new object with prepopulated fields
     *
     * Hint: Does not use the constructor.
     *
     * @param string $class A new instance of this class will be created.
     * @param array  $data  Field name to value data.
     *
     * @return mixed New instance of the class.
     * @throws \ReflectionException
     */
    protected function hydrateClass(string $class, array $data)
    {
        if (false === array_key_exists($class, $this->hydrateClassCache)) {
            $this->hydrateClassCache[$class] = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
        }

        $hydrator = $this->hydrateClosure()->bindTo($this->hydrateClassCache[$class], $class);

        if (false === $hydrator) {
            throw new \RuntimeException(sprintf('Could not bind instance of "%s" to hydrator', $class));
        }

        return $hydrator($data);
    }

    private function hydrateClosure()
    {
        if (null === $this->hydrateClosure) {
            $this->hydrateClosure = function (array $data) {
                foreach ($data as $field => $value) {
                    $this->{$field} = $value;
                }

                return $this;
            };
        }

        return $this->hydrateClosure;
    }

    /**
     * Populate fields in an object
     *
     * @param object $object
     * @param array  $mapping
     *
     * @return mixed
     */
    protected function hydrateObject($object, array $mapping)
    {
        $hydrator = $this->hydrateClosure()->bindTo($object, get_class($object));

        return $hydrator($mapping);
    }
}