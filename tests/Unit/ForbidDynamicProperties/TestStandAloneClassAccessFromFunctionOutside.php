<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesStandAloneClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeStandAloneClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\StdclassStandAloneClassFixture;

use function WpOrg\DynamicPropertiesUtils\Tests\Fixtures\testPropertyAccessFromFunction;
use function WpOrg\DynamicPropertiesUtils\Tests\Fixtures\testPropertyIssetFromFunction;
use function WpOrg\DynamicPropertiesUtils\Tests\Fixtures\testPropertyModificationFromFunction;
use function WpOrg\DynamicPropertiesUtils\Tests\Fixtures\testPropertyUnsetFromFunction;

/**
 * Verify the behaviour of the trait emulates the PHP native behaviour with the exception of
 * dynamic properties being forbidden on all PHP versions.
 *
 * This test class specifically tests the behaviour when accessing/modifying a property from
 * inside a function outside the context of the class containing the property.
 *
 * @covers \WpOrg\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class TestStandAloneClassAccessFromFunctionOutside extends ForbidDynamicPropertiesTestCase
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
     * Make sure the test fixture is loaded.
     *
     * @return void
     */
    public static function set_up_before_class() // phpcs:ignore PSR1.Methods.CamelCapsMethodName -- Cross-version PHPUnit.
    {
        parent::set_up_before_class();
        require_once dirname(dirname(__DIR__)) . '/Fixtures/NonClassFunctionsFixture.php';
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
    public function verifyPropertyIsset($className, $propertyName, $expected)
    {
        $obj = new $className();
        $this->assertSame($expected['isset'], testPropertyIssetFromFunction($obj, $propertyName));
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

                $unused = testPropertyAccessFromFunction($obj, $propertyName);
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $unused = testPropertyAccessFromFunction($obj, $propertyName);
                break;

            default:
                $this->assertSame($expected['get'], testPropertyAccessFromFunction($obj, $propertyName));
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

                testPropertyModificationFromFunction($obj, $propertyName, self::TEST_VALUE_1);

                // Verify the set succeeded.
                $this->assertSame($expected['set'], testPropertyAccessFromFunction($obj, $propertyName));
                break;

            case self::ERR_NO_ACCESS:
                $this->setNoAccessExpectation();

                testPropertyModificationFromFunction($obj, $propertyName, self::TEST_VALUE_1);
                break;

            case self::EXCEPTION_OUTOFBOUNDS:
                $this->expectException(OutOfBoundsException::class);
                $this->expectExceptionMessage(self::EXCEPTION_OUTOFBOUNDS_MSG);

                testPropertyModificationFromFunction($obj, $propertyName, self::TEST_VALUE_1);
                break;

            default:
                testPropertyModificationFromFunction($obj, $propertyName, self::TEST_VALUE_1);

                // Verify the set succeeded.
                $this->assertSame($expected['set'], testPropertyAccessFromFunction($obj, $propertyName));
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

                testPropertyUnsetFromFunction($obj, $propertyName);
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                testPropertyUnsetFromFunction($obj, $propertyName);

                // Verify the unset succeeded and a get now results in the undefined notice.
                $this->assertSame($expected['unset'], testPropertyAccessFromFunction($obj, $propertyName));
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
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            'private property with default value' => array(
                'name'     => 'privatePropertyWithDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
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
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            'private property without default value' => array(
                'name'     => 'privatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
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
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
                ),
            ),
            'unset private property' => array(
                'name'     => 'unsetPrivateProperty',
                'expected' => array(
                    'isset' => false,
                    'get'   => self::ERR_NO_ACCESS,
                    'set'   => self::ERR_NO_ACCESS,
                    'unset' => self::ERR_NO_ACCESS,
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
