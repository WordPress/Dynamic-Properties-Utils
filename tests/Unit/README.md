# About the tests

These tests don't just test the actual functionality of the trait(s) themselves, but they also safeguard that the functionality provided in the trait(s) **_only_** diverts from the PHP native behaviour for those specific situations in which the behaviour is _intended_ to be different.

If we take the `ForbidDynamicProperties` trait as an example, each test class will:
* Test the PHP native behaviour - this codifies the assumptions about the PHP native behaviour and makes sure that the data sets used for the tests are in line with the PHP native behaviour.
* Test the PHP native behaviour if the alternative of extending `stdClass` would be chosen (which maintains support for dynamic properties).
    The same data sets as for the "PHP native" tests are used for this, with one difference: no deprecation notices for creating dynamic properties are expected when these tests are run on PHP 8.2.
* Test the behaviour when the `ForbidDynamicProperties` trait is used.
    Again, the same data sets as for the "PHP native" tests are used, with one difference: for those situations where PHP would start throwing deprecation notices for creating dynamic properties in PHP 8.2, the expectation is changed to expect an `OutOfBounds` exception on **all** PHP versions.

This test set-up is intentional and should be maintained for any additional features being added to this repository.

This set-up will properly safeguarded that the traits emulate the PHP native behaviour on all points, except where a difference is intentional.
It will also serve as an early warning system for future changes in the behaviour regarding property handling in PHP.
