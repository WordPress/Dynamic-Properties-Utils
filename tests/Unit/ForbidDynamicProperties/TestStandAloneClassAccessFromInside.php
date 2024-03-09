<?php

namespace WordPress\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesStandAloneClassFixture;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeStandAloneClassFixture;
use WordPress\DynamicPropertiesUtils\Tests\Fixtures\StdclassStandAloneClassFixture;

/**
 * Verify the behaviour of the trait emulates the PHP native behaviour with the exception of
 * dynamic properties being forbidden on all PHP versions.
 *
 * This test class specifically tests the behaviour when accessing/modifying a property from
 * inside the context of the class containing the property.
 *
 * @covers \WordPress\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class TestStandAloneClassAccessFromInside extends ForbidDynamicPropertiesTestCase
{
    /**
     * Fully qualified name of the fixture to use for the PHP native tests.
     *
     * @var string
     */
    const FIXTURE_PHPNATIVE = PHPNativeStandAloneClassFixture::class;

    /**
     * Fully qualified name of the fixture to use for the stdClass tests.
     *
     * @var string
     */
    const FIXTURE_STDCLASS = StdclassStandAloneClassFixture::class;

    /**
     * Fully qualified name of the fixture to use for the ForbidDynamicProperties tests.
     *
     * @var string
     */
    const FIXTURE_TRAIT = ForbidDynamicPropertiesStandAloneClassFixture::class;

    /**
     * List of data set names for properties which would be dynamically set.
     *
     * @var string[]
     */
    const DYNAMIC = array(
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
        $this->assertSame($expected['isset'], $obj->testPropertyIsset($propertyName));
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
            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $unused = $obj->testPropertyAccess($propertyName);
                break;

            default:
                $this->assertSame($expected['get'], $obj->testPropertyAccess($propertyName));
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

            case self::EXCEPTION_OUTOFBOUNDS:
                $this->expectException(OutOfBoundsException::class);
                $this->expectExceptionMessage(self::EXCEPTION_OUTOFBOUNDS_MSG);

                $obj->testPropertyModification($propertyName, self::TEST_VALUE_1);
                break;

            default:
                $obj->testPropertyModification($propertyName, self::TEST_VALUE_1);

                // Verify the set succeeded.
                $this->assertSame($expected['set'], $obj->testPropertyAccess($propertyName));
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
            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $obj->testPropertyUnset($propertyName);

                // Verify the unset succeeded and a get now results in the undefined notice.
                $this->assertSame($expected['unset'], $obj->testPropertyAccess($propertyName));
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
            'public property with default value' => array(
                'name'     => 'publicPropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'public with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'protected property with default value' => array(
                'name'     => 'protectedPropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'protected with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'private property with default value' => array(
                'name'     => 'privatePropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'private with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'public property without default value' => array(
                'name'     => 'publicPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'protected property without default value' => array(
                'name'     => 'protectedPropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'private property without default value' => array(
                'name'     => 'privatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'unset public property' => array(
                'name'     => 'unsetPublicProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'unset protected property' => array(
                'name'     => 'unsetProtectedProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            'unset private property' => array(
                'name'     => 'unsetPrivateProperty',
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
