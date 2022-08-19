<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Fixtures;

class StdclassChildClassFixture extends StdclassParentClassFixture
{
    public $childPublicPropertyWithDefault = 'child public with default';
    protected $childProtectedPropertyWithDefault = 'child protected with default';
    private $childPrivatePropertyWithDefault = 'child private with default';

    public $childPublicPropertyWithoutDefault;
    protected $childProtectedPropertyWithoutDefault;
    private $childPrivatePropertyWithoutDefault;

    public $childUnsetPublicProperty = 'child public set';
    protected $childUnsetProtectedProperty = 'child protected set';
    private $childUnsetPrivateProperty = 'child private set';

    public function __construct()
    {
        unset($this->childUnsetPublicProperty, $this->childUnsetProtectedProperty, $this->childUnsetPrivateProperty);
        parent::__construct();
    }

    public function testChildPropertyIsset($name)
    {
        return isset($this->$name);
    }

    public function testChildPropertyAccess($name)
    {
        return $this->$name;
    }

    public function testChildPropertyModification($name, $value)
    {
        $this->$name = $value;
    }

    public function testChildPropertyUnset($name)
    {
        unset($this->$name);
    }
}
