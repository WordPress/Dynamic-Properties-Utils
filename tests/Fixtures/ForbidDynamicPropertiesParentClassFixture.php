<?php

namespace WpOrg\DynamicPropertiesUtils\Tests\Fixtures;

use WpOrg\DynamicPropertiesUtils\ForbidDynamicProperties;

class ForbidDynamicPropertiesParentClassFixture
{
    use ForbidDynamicProperties;

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
}
