<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Fixtures;

class PHPNativeParentClassFixture
{
    public $parentPublicPropertyWithDefault = 'parent public with default';
    protected $parentProtectedPropertyWithDefault = 'parent protected with default';
    private $parentPrivatePropertyWithDefault = 'parent private with default';

    public $parentPublicPropertyWithoutDefault;
    protected $parentProtectedPropertyWithoutDefault;
    private $parentPrivatePropertyWithoutDefault;

    public $parentUnsetPublicProperty = 'parent public set';
    protected $parentUnsetProtectedProperty = 'parent protected set';
    private $parentUnsetPrivateProperty = 'parent private set';

    public function __construct()
    {
        unset($this->parentUnsetPublicProperty, $this->parentUnsetProtectedProperty, $this->parentUnsetPrivateProperty);
    }

    public function testParentPropertyIsset($name)
    {
        return isset($this->$name);
    }

    public function testParentPropertyAccess($name)
    {
        return $this->$name;
    }

    public function testParentPropertyModification($name, $value)
    {
        $this->$name = $value;
    }

    public function testParentPropertyUnset($name)
    {
        unset($this->$name);
    }
}
