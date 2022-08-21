<?php

namespace WpOrg\DynamicPropertiesUtils\Tests;

use Error;
use PHPUnit\Runner\Version as PHPUnit_Version;
use PHPUnit_Runner_Version;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;

abstract class TestCase extends Polyfill_TestCase
{
    const TEST_VALUE_1 = 'Testing-1-2-3';
    const TEST_VALUE_2 = 12345;

    const ERR_DYN_PROPERTY = 'PHP Deprecation: dynamic property creation';
    const ERR_DYN_PROPERTY_MSG = 'Creation of dynamic property %s::$%s is deprecated';

    const ERR_NO_ACCESS = 'PHP Error: no access';
    const ERR_NO_ACCESS_MSG_REGEX = '`Cannot access (?:private|protected) property`';

    const ERR_UNDEFINED = 'PHP Error: undefined property';
    const ERR_UNDEFINED_MSG = 'Undefined property: ';

    const EXCEPTION_OUTOFBOUNDS = 'Exception: out of bounds';
    const EXCEPTION_OUTOFBOUNDS_MSG = 'Dynamic properties are not supported for class ';

    /**
     * Helper function to work around a PHPUnit bug.
     *
     * A limited number of PHPUnit versions do not recognize catchable PHP Errors
     * as native PHP errors.
     * Whether this comes into play is highly dependent on how PHP itself throws a particular error,
     * which may be different in different PHP versions.
     * For that reason, the polyfills cannot work around this as there are too many variables in play
     * (PHP version, PHPUnit version, the specific error).
     *
     * This helper function works around it for the particular PHP native error where this library
     * runs into this bug.
     *
     * Note: in reality, there is a more specific range of PHPUnit versions affected, including some
     * in the PHPUnit 8.x and 9.x series, however, CI for this package will always use the most
     * appropriate PHPUnit version and the latest version of that, so in practice, this bug will
     * only be seen on PHPUnit < 8.x.
     */
    protected function setNoAccessExpectation()
    {
        if (version_compare($this->getPHPUnitVersion(), '8.0.0', '<')) {
            // PHPUnit < 8.0.0.
            $this->expectException(Error::class);
            $this->expectExceptionMessageMatches(self::ERR_NO_ACCESS_MSG_REGEX);
            return;
        }

        $this->expectError();
        $this->expectErrorMessageMatches(self::ERR_NO_ACCESS_MSG_REGEX);
    }

    /**
     * Retrieve the PHPUnit version id.
     *
     * @return string Version number as a string.
     */
    private function getPHPUnitVersion()
    {
        if (class_exists(PHPUnit_Version::class)) {
            return PHPUnit_Version::id();
        }

        if (class_exists(PHPUnit_Runner_Version::class)) {
            return PHPUnit_Runner_Version::id();
        }

        return '0';
    }
}
