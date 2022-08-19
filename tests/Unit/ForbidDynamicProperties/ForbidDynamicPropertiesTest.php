<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Unit\ForbidDynamicProperties;

use OutOfBoundsException;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\ForbidDynamicPropertiesChildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\PHPNativeChildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\Fixtures\StdclassChildClassFixture;
use WpOrg\DynamicPropertiesUtils\Tests\TestCase;

/**
 * @covers \WpOrg\DynamicPropertiesUtils\ForbidDynamicProperties
 */
final class ForbidDynamicPropertiesTest extends TestCase
{
    /*
     * TO DO:
     * - Add from_outside_child_class test set
     * - Add parent-child-grandchild test set with variations
     */

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassPhpNative
     *
     */
    public function testPropertyIssetFromInsideChildClassPhpNative($name, $expected)
    {
        $this->verifyPropertyIssetFromInsideChildClass(PHPNativeChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassPhpNative
     *
     */
    public function testPropertyGetFromInsideChildClassPhpNative($name, $expected)
    {
        $this->verifyPropertyGetFromInsideChildClass(PHPNativeChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassPhpNative
     *
     */
    public function testPropertySetFromInsideChildClassPhpNative($name, $expected)
    {
        $this->verifyPropertySetFromInsideChildClass(PHPNativeChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassPhpNative
     *
     */
    public function testPropertyUnsetFromInsideChildClassPhpNative($name, $expected)
    {
        $this->verifyPropertyUnsetFromInsideChildClass(PHPNativeChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithStdclass
     *
     */
    public function testPropertyIssetFromInsideChildClassWithStdclass($name, $expected)
    {
        $this->verifyPropertyIssetFromInsideChildClass(StdclassChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithStdclass
     *
     */
    public function testPropertyGetFromInsideChildClassWithStdclass($name, $expected)
    {
        $this->verifyPropertyGetFromInsideChildClass(StdclassChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithStdclass
     *
     */
    public function testPropertySetFromInsideChildClassWithStdclass($name, $expected)
    {
        $this->verifyPropertySetFromInsideChildClass(StdclassChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithStdclass
     *
     */
    public function testPropertyUnsetFromInsideChildClassWithStdclass($name, $expected)
    {
        $this->verifyPropertyUnsetFromInsideChildClass(StdclassChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithTrait
     *
     */
    public function testPropertyIssetFromInsideChildClassWithTrait($name, $expected)
    {
        $this->verifyPropertyIssetFromInsideChildClass(ForbidDynamicPropertiesChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithTrait
     *
     */
    public function testPropertyGetFromInsideChildClassWithTrait($name, $expected)
    {
        $this->verifyPropertyGetFromInsideChildClass(ForbidDynamicPropertiesChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithTrait
     *
     */
    public function testPropertySetFromInsideChildClassWithTrait($name, $expected)
    {
        $this->verifyPropertySetFromInsideChildClass(ForbidDynamicPropertiesChildClassFixture::class, $name, $expected);
    }

    /**
     *
     * @dataProvider dataPropertyAccessFromInsideChildClassWithTrait
     *
     */
    public function testPropertyUnsetFromInsideChildClassWithTrait($name, $expected)
    {
        $this->verifyPropertyUnsetFromInsideChildClass(ForbidDynamicPropertiesChildClassFixture::class, $name, $expected);
    }

    /**
     *
     *
     */
    public function verifyPropertyIssetFromInsideChildClass($className, $propertyName, $expected)
    {
        $obj = new $className();
        $this->assertSame($expected['isset'], $obj->testPropertyIsset($propertyName));
    }

    /**
     *
     *
     */
    public function verifyPropertyGetFromInsideChildClass($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['get']) {
/*
            case self::ERR_NO_ACCESS:
                $this->expectError();
                $this->expectErrorMessageMatches( self::ERR_NO_ACCESS_MSG_REGEX );

                $unused = $obj->$propertyName;
                break;
*/
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
    public function verifyPropertySetFromInsideChildClass($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['set']) {
/*          case self::ERR_NO_ACCESS:
                $this->expectError();
                $this->expectErrorMessageMatches( self::ERR_NO_ACCESS_MSG_REGEX );

                $unused = $obj->$propertyName;
                break;
*/
/*
            case self::ERR_UNDEFINED:
                if ( PHP_VERSION_ID >= 80000 ) {
                    $this->expectWarning();
                    $this->expectWarningMessage( self::ERR_UNDEFINED_MSG );
                } else {
                    $this->expectNotice();
                    $this->expectNoticeMessage( self::ERR_UNDEFINED_MSG );
                }

                $unused = $obj->$propertyName;
                break;
*/
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
    public function verifyPropertyUnsetFromInsideChildClass($className, $propertyName, $expected)
    {
        $obj = new $className();

        switch ($expected['unset']) {
/*
            case self::ERR_NO_ACCESS:
                $this->expectError();
                $this->expectErrorMessageMatches( self::ERR_NO_ACCESS_MSG_REGEX );

                unset( $obj->$propertyName );
                break;
*/
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
    public function dataPropertyAccessFromInsideChildClassPhpNative()
    {
        return array(
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
    public function dataPropertyAccessFromInsideChildClassWithStdclass()
    {
        return $this->dataPropertyAccessFromInsideChildClassPhpNative();
    }

    /**
     * Data provider.
     *
     * @var array
     */
    public function dataPropertyAccessFromInsideChildClassWithTrait()
    {
        $data = $this->dataPropertyAccessFromInsideChildClassPhpNative();
        $data['[Parent] private property with default value']['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        $data['[Parent] private property without default value']['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        $data['[Parent] unset private property']['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        $data['undeclared property']['expected']['set'] = self::EXCEPTION_OUTOFBOUNDS;
        return $data;
    }
}
