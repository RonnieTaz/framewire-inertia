<?php

namespace Framewire\Inertia\Traits;

use ReflectionException;
use ReflectionObject;

trait ReflectsProperties
{
    /**
     * @throws ReflectionException
     */
    protected function getExpectedClassType(object $object, string $propertyName): string
    {
        $reflection = new ReflectionObject($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getType()->getName();
    }
}
