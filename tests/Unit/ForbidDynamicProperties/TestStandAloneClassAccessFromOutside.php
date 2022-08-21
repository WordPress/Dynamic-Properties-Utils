<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesStandAloneClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeStandAloneClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\StdclassStandAloneClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\TestCase;

/**
 * @covers \WpOrg\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class TestStandAloneClassAccessFromOutside extends TestCase
{
    /**
     * List of data set names for properties which would be dynamically set.
     *
     * @var string[]
     */
    const DYNAMIC = array(
        'undeclared property',
    );

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyIssetPhpNative($name, $expected)
    {
        $this->verifyPropertyIsset(PHPNativeStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyGetPhpNative($name, $expected)
    {
        $this->verifyPropertyGet(PHPNativeStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertySetPhpNative($name, $expected)
    {
        $this->verifyPropertySet(PHPNativeStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyUnsetPhpNative($name, $expected)
    {
        $this->verifyPropertyUnset(PHPNativeStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyIssetWithStdclass($name, $expected)
    {
        $this->verifyPropertyIsset(StdclassStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyGetWithStdclass($name, $expected)
    {
        $this->verifyPropertyGet(StdclassStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertySetWithStdclass($name, $expected)
    {
        $this->verifyPropertySet(StdclassStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyUnsetWithStdclass($name, $expected)
    {
        $this->verifyPropertyUnset(StdclassStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyIssetWithTrait($name, $expected)
    {
        $this->verifyPropertyIsset(ForbidDynamicPropertiesStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyGetWithTrait($name, $expected)
    {
        $this->verifyPropertyGet(ForbidDynamicPropertiesStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertySetWithTrait($name, $expected)
    {
        $this->verifyPropertySet(ForbidDynamicPropertiesStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyUnsetWithTrait($name, $expected)
    {
        $this->verifyPropertyUnset(ForbidDynamicPropertiesStandAloneClassFixture::class, $name, $expected);
    }

    /**
     *
     *
     */
    public function verifyPropertyIsset($className, $propertyName, $expected)
    {
        $obj = new $className();
        $this->assertSame($expected['isset'], isset($obj->$propertyName));
    }

    /**
     *
     *
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
     *
     *
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
     *
     *
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
     * @var array
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
     * @var array
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
     * @var array
     */
    public function dataPropertyAccessWithStdclass()
    {
        return $this->dataPropertyAccessBase();
    }

    /**
     * Data provider.
     *
     * @var array
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
