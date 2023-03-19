<?php

namespace WordPress\DynamicPropertiesUtils\Tests\Fixtures;

class ForbidDynamicPropertiesGrandchildClassFixture extends ForbidDynamicPropertiesChildClassFixture
{
    public $grandchildPublicPropertyWithDefault = 'grandchild public with default';
    protected $grandchildProtectedPropertyWithDefault = 'grandchild protected with default';
    private $grandchildPrivatePropertyWithDefault = 'grandchild private with default';

    public $grandchildPublicPropertyWithoutDefault;
    protected $grandchildProtectedPropertyWithoutDefault;
    private $grandchildPrivatePropertyWithoutDefault;

    public $grandchildUnsetPublicProperty = 'grandchild public set';
    protected $grandchildUnsetProtectedProperty = 'grandchild protected set';
    private $grandchildUnsetPrivateProperty = 'grandchild private set';

    public function __construct()
    {
        unset($this->grandchildUnsetPublicProperty, $this->grandchildUnsetProtectedProperty, $this->grandchildUnsetPrivateProperty);
        parent::__construct();
    }

    public function testGrandchildPropertyIsset($name)
    {
        return isset($this->$name);
    }

    public function testGrandchildPropertyAccess($name)
    {
        return $this->$name;
    }

    public function testGrandchildPropertyModification($name, $value)
    {
        $this->$name = $value;
    }

    public function testGrandchildPropertyUnset($name)
    {
        unset($this->$name);
    }
}
