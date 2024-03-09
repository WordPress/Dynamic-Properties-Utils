<?php

namespace WordPress\DynamicPropertiesUtils;

use OutOfBoundsException;
use ReflectionProperty;

trait ForbidDynamicProperties
{
    public function __set($name, $value)
    {
        if (!static::propertyExists($name)) {
            throw new OutOfBoundsException('Dynamic properties are not supported for class ' . static::class . '. Property $' . $name . ' cannot be set.');
        }

        if ($this->propertyCanBeAccessed($name)) {
            $this->$name = $value;
        }
    }

    private function propertyCanBeAccessed($name)
    {
        if ($this->isPublicProperty($name)) {
            return true;
        }

        $backtraceNumber = 3;
        $backtraceIndex = $backtraceNumber - 1;

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $backtraceNumber);

        if (!isset($backtrace[$backtraceIndex]) || !isset($backtrace[$backtraceIndex]['class'])) {
            return false;
        }

        $class = $backtrace[$backtraceIndex]['class'];

        return is_a(static::class, $class, true);
    }

    private function isPublicProperty($name)
    {
        return (new ReflectionProperty($this, $name))->isPublic();
    }

    private static function propertyExists($name)
    {
        $class = static::class;

        do {
            $exists = property_exists($class, $name);
            if ($exists) {
                return true;
            }
        } while ($class = get_parent_class($class));

        return false;
    }
}
