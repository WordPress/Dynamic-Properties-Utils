<?php

/**
 * Fixture to test triggering the magic methods from outside any class context.
 */

namespace WordPress\DynamicPropertiesUtils\Tests\Fixtures;

function testPropertyIssetFromFunction($objectInstance, $name)
{
    return isset($objectInstance->$name);
}

function testPropertyAccessFromFunction($objectInstance, $name)
{
    return $objectInstance->$name;
}

function testPropertyModificationFromFunction($objectInstance, $name, $value)
{
    $objectInstance->$name = $value;
}

function testPropertyUnsetFromFunction($objectInstance, $name)
{
    unset($objectInstance->$name);
}
