<?php

namespace WpOrg\DynamicPropertiesUtils\Tests;

use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;

abstract class TestCase extends Polyfill_TestCase
{
    const TEST_VALUE_1 = 'Testing-1-2-3';
    const TEST_VALUE_2 = 12345;

    const ERR_NO_ACCESS = 'PHP Error: no access';
    const ERR_NO_ACCESS_MSG_REGEX = '`Cannot access (?:private|protected) property`';

    const ERR_UNDEFINED = 'PHP Error: undefined property';
    const ERR_UNDEFINED_MSG = 'Undefined property: ';

    const ERR_OUTOFBOUNDS = 'Exception: out of bounds';
    const ERR_OUTOFBOUNDS_MSG = 'Dynamic properties are not supported for class ';
}
