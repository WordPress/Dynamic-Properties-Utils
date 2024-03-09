<?php

namespace WordPress\DynamicPropertiesUtils\Tests\Fixtures;

class PHPNativeStandAloneClassFixture
{
    public $publicPropertyWithDefault = 'public with default';
    protected $protectedPropertyWithDefault = 'protected with default';
    private $privatePropertyWithDefault = 'private with default';

    public $publicPropertyWithoutDefault;
    protected $protectedPropertyWithoutDefault;
    private $privatePropertyWithoutDefault;

    public $unsetPublicProperty = 'public set';
    protected $unsetProtectedProperty = 'protected set';
    private $unsetPrivateProperty = 'private set';

    public function __construct()
    {
        unset($this->unsetPublicProperty, $this->unsetProtectedProperty, $this->unsetPrivateProperty);
    }

    public function testPropertyIsset($name)
    {
        return isset($this->$name);
    }

    public function testPropertyAccess($name)
    {
        return $this->$name;
    }

    public function testPropertyModification($name, $value)
    {
        $this->$name = $value;
    }

    public function testPropertyUnset($name)
    {
        unset($this->$name);
    }
}
