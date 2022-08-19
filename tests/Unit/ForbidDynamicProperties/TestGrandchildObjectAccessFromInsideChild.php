<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesGrandchildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeGrandchildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\StdclassGrandchildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\TestCase;

/**
 * @covers \WpOrg\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class TestGrandchildObjectAccessFromInsideChild extends TestCase
{
    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyIssetPhpNative($name, $expected)
    {
        $this->verifyPropertyIsset(PHPNativeGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyGetPhpNative($name, $expected)
    {
        $this->verifyPropertyGet(PHPNativeGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertySetPhpNative($name, $expected)
    {
        $this->verifyPropertySet(PHPNativeGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessPhpNative
     *
     */
    public function testPropertyUnsetPhpNative($name, $expected)
    {
        $this->verifyPropertyUnset(PHPNativeGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyIssetWithStdclass($name, $expected)
    {
        $this->verifyPropertyIsset(StdclassGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyGetWithStdclass($name, $expected)
    {
        $this->verifyPropertyGet(StdclassGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertySetWithStdclass($name, $expected)
    {
        $this->verifyPropertySet(StdclassGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithStdclass
     *
     */
    public function testPropertyUnsetWithStdclass($name, $expected)
    {
        $this->verifyPropertyUnset(StdclassGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyIssetWithTrait($name, $expected)
    {
        $this->verifyPropertyIsset(ForbidDynamicPropertiesGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyGetWithTrait($name, $expected)
    {
        $this->verifyPropertyGet(ForbidDynamicPropertiesGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertySetWithTrait($name, $expected)
    {
        $this->verifyPropertySet(ForbidDynamicPropertiesGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessWithTrait
     *
     */
    public function testPropertyUnsetWithTrait($name, $expected)
    {
        $this->verifyPropertyUnset(ForbidDynamicPropertiesGrandchildClassFixture::class, $name, $expected);
    }

    /**
     *
     *
     */
    public function verifyPropertyIsset($className, $propertyName, $expected)
    {
        $obj = new $className();
        $this->assertSame($expected['isset'], $obj->testChildPropertyIsset($propertyName));
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
                $this->expectError();
                $this->expectErrorMessageMatches(self::ERR_NO_ACCESS_MSG_REGEX);

                $unused = $obj->testChildPropertyAccess($propertyName);
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $unused = $obj->testChildPropertyAccess($propertyName);
                break;

            default:
                $this->assertSame($expected['get'], $obj->testChildPropertyAccess($propertyName));
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
            case self::ERR_NO_ACCESS:
                $this->expectError();
                $this->expectErrorMessageMatches(self::ERR_NO_ACCESS_MSG_REGEX);

                $obj->testChildPropertyModification($propertyName, self::TEST_VALUE_1);
                break;

            case self::EXCEPTION_OUTOFBOUNDS:
                $this->expectException(OutOfBoundsException::class);
                $this->expectExceptionMessage(self::EXCEPTION_OUTOFBOUNDS_MSG);

                $obj->testChildPropertyModification($propertyName, self::TEST_VALUE_1);
                break;

            default:
                $obj->testChildPropertyModification($propertyName, self::TEST_VALUE_1);

                // Verify the set succeeded.
                $this->assertSame($expected['set'], $obj->testChildPropertyAccess($propertyName));
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
                $this->expectError();
                $this->expectErrorMessageMatches(self::ERR_NO_ACCESS_MSG_REGEX);

                $obj->testChildPropertyUnset($propertyName);
                break;

            case self::ERR_UNDEFINED:
                if (PHP_VERSION_ID >= 80000) {
                    $this->expectWarning();
                    $this->expectWarningMessage(self::ERR_UNDEFINED_MSG);
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage(self::ERR_UNDEFINED_MSG);
                }

                $obj->testChildPropertyUnset($propertyName);

                // Verify the unset succeeded and a get now results in the undefined notice.
                $this->assertSame($expected['unset'], $obj->testChildPropertyAccess($propertyName));
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
                    'isset' => true,
                    'get'   => 'grandchild protected with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'isset' => true,
                    'get'   => 'child protected with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] private property with default value' => array(
                'name'     => 'childPrivatePropertyWithDefault',
                'expected' => array(
                    'isset' => true,
                    'get'   => 'child private with default',
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
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
                ),
            ),
            '[Child] private property without default value' => array(
                'name'     => 'childPrivatePropertyWithoutDefault',
                'expected' => array(
                    'isset' => false,
                    'get'   => null,
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
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'isset' => true,
                    'get'   => 'parent protected with default',
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'get'   => null,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
                    'get'   => self::ERR_UNDEFINED,
                    'set'   => self::TEST_VALUE_1,
                    'unset' => self::ERR_UNDEFINED,
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
        $dynamic = [
            '[Parent] private property with default value',
            '[Parent] private property without default value',
            '[Parent] unset private property',
            'undeclared property',
        ];

        $data = $this->dataPropertyAccessPhpNative();
        foreach ($dynamic as $name) {
            $data[$name]['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        }

        return $data;
    }
}
