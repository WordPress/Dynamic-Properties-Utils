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
final class TestStandAloneClassAccessFromInside extends TestCase
{
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
        $this->assertSame($expected['isset'], $obj->testPropertyIsset($propertyName));
    }

    /**
     *
     *
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
     *
     *
     */
    public function verifyPropertySet($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['set']) {
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
     *
     *
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
     * Data provider.
     *
     * @var array
     */
    public function dataPropertyAccessPhpNative()
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
     * @var array
     */
    public function dataPropertyAccessWithStdclass()
    {
        return $this->dataPropertyAccessPhpNative();
    }

    /**
     * Data provider.
     *
     * @var array
     */
    public function dataPropertyAccessWithTrait()
    {
        $data = $this->dataPropertyAccessPhpNative();
        $data['undeclared property']['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        return $data;
    }
}
