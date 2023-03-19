<?php

namespace WordPress\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesGrandchildClassFixture;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeGrandchildClassFixture;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\StdclassGrandchildClassFixture;

/**
 * Verify the behaviour of the trait emulates the PHP native behaviour with the exception of
 * dynamic properties being forbidden on all PHP versions.
 *
 * This test class specifically tests the behaviour when accessing/modifying a property from
 * outside the context of the class containing the property, where the class being used
 * is part of a hierarchical class structure with the object being a "grandchild" and properties
 * existing in the parent, child and the grandchild classes.
 *
 * @covers \WordPress\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class TestGrandchildObjectAccessFromOutside extends ForbidDynamicPropertiesTestCase
{
    /**
     * Fully qualified name of the fixture to use for the PHP native tests.
     *
     * @var string
     */
    const FIXTURE_PHPNATIVE = PHPNativeGrandchildClassFixture::class;

    /**
     * Fully qualified name of the fixture to use for the stdClass tests.
     *
     * @var string
     */
    const FIXTURE_STDCLASS = StdclassGrandchildClassFixture::class;

    /**
     * Fully qualified name of the fixture to use for the ForbidDynamicProperties tests.
     *
     * @var string
     */
    const FIXTURE_TRAIT = ForbidDynamicPropertiesGrandchildClassFixture::class;

    /**
     * List of data set names for properties which would be dynamically set.
     *
     * @var string[]
     */
    const DYNAMIC = array(
        '[Child] private property with default value',
        '[Child] private property without default value',
        '[Child] unset private property',
        '[Parent] private property with default value',
        '[Parent] private property without default value',
        '[Parent] unset private property',
        'undeclared property',
    );

    /**
     * Verify the behaviour when calling isset() on a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    public function verifyPropertyIsset($className, $propertyName, $expected)
    {
        $obj = new $className();
        $this->assertSame($expected['isset'], isset($obj->$propertyName));
    }

    /**
     * Verify the behaviour when accessing a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    public function verifyPropertyGet($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['get']) {
            case self::ERR_NO_ACCESS:
                $this->setNoAccessExpectation();

                $unused = $obj->$propertyName;
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $unused = $obj->$propertyName;
                break;

            default:
                $this->assertSame($expected['get'], $obj->$propertyName);
                break;
        }
    }

    /**
     * Verify the behaviour when modifying a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    public function verifyPropertySet($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['set']) {
            case self::ERR_DYN_PROPERTY:
                $this->expectDeprecation();
                $this->expectDeprecationMessage(sprintf(self::ERR_DYN_PROPERTY_MSG, $className, $propertyName));

                $obj->$propertyName = self::TEST_VALUE_1;

                // Verify the set succeeded.
                $this->assertSame($expected['set'], $obj->$propertyName);
                break;

            case self::ERR_NO_ACCESS:
                $this->setNoAccessExpectation();

                $obj->$propertyName = self::TEST_VALUE_1;
                break;

            case self::EXCEPTION_OUTOFBOUNDS:
                $this->expectException(OutOfBoundsException::class);
                $this->expectExceptionMessage(self::EXCEPTION_OUTOFBOUNDS_MSG);

                $obj->$propertyName = self::TEST_VALUE_1;
                break;

            default:
                $obj->$propertyName = self::TEST_VALUE_1;

                // Verify the set succeeded.
                $this->assertSame($expected['set'], $obj->$propertyName);
                break;
        }
    }

    /**
     * Verify the behaviour of calling unset() on a property.
     *
     * @param string $className    The class (test fixture) to instantiate for this test.
     * @param string $propertyName Property name.
     * @param array  $expected     Expectations.
     *
     * @return void
     */
    public function verifyPropertyUnset($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['unset']) {
            case self::ERR_NO_ACCESS:
                $this->setNoAccessExpectation();

                unset($obj->$propertyName);
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                unset($obj->$propertyName);

                // Verify the unset succeeded and a get now results in the undefined notice.
                $this->assertSame($expected['unset'], $obj->$propertyName);
                break;

            default:
                $this->fail('Invalid expectation set');
                break;
        }
    }

    /**
     * Base data sets for data providers.
     *
     * @return array
     */
    public function dataPropertyAccessBase()
    {
        return array(
            '[Grandchild] public property with default value' => array(
                'name'     => 'grandchildPublicPropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'grandchild public with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Grandchild] protected property with default value' => array(
                'name'     => 'grandchildProtectedPropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Grandchild] private property with default value' => array(
                'name'     => 'grandchildPrivatePropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Grandchild] public property without default value' => array(
                'name'     => 'grandchildPublicPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Grandchild] protected property without default value' => array(
                'name'     => 'grandchildProtectedPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Grandchild] private property without default value' => array(
                'name'     => 'grandchildPrivatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Grandchild] unset public property' => array(
                'name'     => 'grandchildUnsetPublicProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Grandchild] unset protected property' => array(
                'name'     => 'grandchildUnsetProtectedProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Grandchild] unset private property' => array(
                'name'     => 'grandchildUnsetPrivateProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Child] public property with default value' => array(
                'name'     => 'childPublicPropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'child public with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] protected property with default value' => array(
                'name'     => 'childProtectedPropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Child] private property with default value' => array(
                'name'     => 'childPrivatePropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] public property without default value' => array(
                'name'     => 'childPublicPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] protected property without default value' => array(
                'name'     => 'childProtectedPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Child] private property without default value' => array(
                'name'     => 'childPrivatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] unset public property' => array(
                'name'     => 'childUnsetPublicProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] unset protected property' => array(
                'name'     => 'childUnsetProtectedProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Child] unset private property' => array(
                'name'     => 'childUnsetPrivateProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] public property with default value' => array(
                'name'     => 'parentPublicPropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'parent public with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] protected property with default value' => array(
                'name'     => 'parentProtectedPropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Parent] private property with default value' => array(
                'name'     => 'parentPrivatePropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] public property without default value' => array(
                'name'     => 'parentPublicPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] protected property without default value' => array(
                'name'     => 'parentProtectedPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Parent] private property without default value' => array(
                'name'     => 'parentPrivatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] unset public property' => array(
                'name'     => 'parentUnsetPublicProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Parent] unset protected property' => array(
                'name'     => 'parentUnsetProtectedProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            '[Parent] unset private property' => array(
                'name'     => 'parentUnsetPrivateProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'undeclared property' => array(
                'name'     => 'undeclaredProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
        );
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataPropertyAccessPhpNative()
    {
        $data = $this->dataPropertyAccessBase();

        if (PHP_VERSION_ID >= 80200) {
            foreach (self::DYNAMIC as $name) {
                $data[$name]['expected']['set'] = self::ERR_DYN_PROPERTY;
            }
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataPropertyAccessWithStdclass()
    {
        return $this->dataPropertyAccessBase();
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataPropertyAccessWithTrait()
    {
        $data = $this->dataPropertyAccessBase();
        foreach (self::DYNAMIC as $name) {
            $data[$name]['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        }

        return $data;
    }
}
