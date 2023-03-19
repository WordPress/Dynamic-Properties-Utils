<?php

namespace WordPress\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use WordPress\DynamicPropertiesUtils\Tests\Unit\TestCase;

/**
 * ForbidDynamicProperties base test case.
 *
 * This test case expects three constants to be declared in implementing test classes:
 * - `FIXTURE_PHPNATIVE` containing the fully qualified name of the test fixture to use for the PHP native tests.
 * - `FIXTURE_STDCLASS` containing the fully qualified name of the test fixture to use for the stdClass tests.
 * - `FIXTURE_TRAIT` containing the fully qualified name of the test fixture to use for the ForbidDynamicProperties trait tests.
 */
abstract class ForbidDynamicPropertiesTestCase extends TestCase
{
    /**
     * Verify the PHP native behaviour of calling isset() on a property.
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyIssetPhpNative($name, $expected)
    {
        $this->verifyPropertyIsset(static::FIXTURE_PHPNATIVE, $name, $expected);
    }

    /**
     * Verify the PHP native behaviour when accessing a property.
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyGetPhpNative($name, $expected)
    {
        $this->verifyPropertyGet(static::FIXTURE_PHPNATIVE, $name, $expected);
    }

    /**
     * Verify the PHP native behaviour when modifying a property.
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertySetPhpNative($name, $expected)
    {
        $this->verifyPropertySet(static::FIXTURE_PHPNATIVE, $name, $expected);
    }

    /**
     * Verify the PHP native behaviour of calling unset() on a property.
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyUnsetPhpNative($name, $expected)
    {
        $this->verifyPropertyUnset(static::FIXTURE_PHPNATIVE, $name, $expected);
    }

    /**
     * Verify the behaviour of calling isset() on a property when a class extends stdClass.
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyIssetWithStdclass($name, $expected)
    {
        $this->verifyPropertyIsset(static::FIXTURE_STDCLASS, $name, $expected);
    }

    /**
     * Verify the behaviour when accessing a property when a class extends stdClass.
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyGetWithStdclass($name, $expected)
    {
        $this->verifyPropertyGet(static::FIXTURE_STDCLASS, $name, $expected);
    }

    /**
     * Verify the behaviour when modifying a property when a class extends stdClass.
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertySetWithStdclass($name, $expected)
    {
        $this->verifyPropertySet(static::FIXTURE_STDCLASS, $name, $expected);
    }

    /**
     * Verify the behaviour of calling unset() on a property when a class extends stdClass.
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyUnsetWithStdclass($name, $expected)
    {
        $this->verifyPropertyUnset(static::FIXTURE_STDCLASS, $name, $expected);
    }

    /**
     * Verify the behaviour of calling isset() on a property when a class uses the ForbidDynamicProperties trait.
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyIssetWithTrait($name, $expected)
    {
        $this->verifyPropertyIsset(static::FIXTURE_TRAIT, $name, $expected);
    }

    /**
     * Verify the behaviour when accessing a property when a class uses the ForbidDynamicProperties trait.
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyGetWithTrait($name, $expected)
    {
        $this->verifyPropertyGet(static::FIXTURE_TRAIT, $name, $expected);
    }

    /**
     * Verify the behaviour when modifying a property when a class uses the ForbidDynamicProperties trait.
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertySetWithTrait($name, $expected)
    {
        $this->verifyPropertySet(static::FIXTURE_TRAIT, $name, $expected);
    }

    /**
     * Verify the behaviour of calling unset() on a property when a class uses the ForbidDynamicProperties trait.
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     * @param string $name     Property name.
     * @param array  $expected Expectations.
     *
     * @return void
     */
    public function testPropertyUnsetWithTrait($name, $expected)
    {
        $this->verifyPropertyUnset(static::FIXTURE_TRAIT, $name, $expected);
    }

    /**
     * Verify the behaviour when calling isset() on a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    abstract public function verifyPropertyIsset($className, $propertyName, $expected);

    /**
     * Verify the behaviour when accessing a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    abstract public function verifyPropertyGet($className, $propertyName, $expected);

    /**
     * Verify the behaviour when modifying a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    abstract public function verifyPropertySet($className, $propertyName, $expected);

    /**
     * Verify the behaviour of calling unset() on a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    abstract public function verifyPropertyUnset($className, $propertyName, $expected);

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    abstract public function dataPropertyAccessPhpNative();

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    abstract public function dataPropertyAccessWithStdclass();

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    abstract public function dataPropertyAccessWithTrait();
}
