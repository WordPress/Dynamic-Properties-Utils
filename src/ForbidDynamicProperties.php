<?php

namespace WpOrg\DynamicPropertiesUtils;

use Error;
use OutOfBoundsException;
use ReflectionException;
use ReflectionProperty;

/**
 * Trait to forbid the use of dynamic properties, while emulating the PHP native behaviour
 * for everything else.
 *
 * {@internal Property names used within the trait are prefixed with an acronym for the class
 * to minimize the risk of property naming conflicts with classes _using_ this trait.
 * Aside from the `__set()` method, the same goes for the methods to minimize the risk
 * of classes _using_ the trait accidentally overloading any of the methods in the trait.}
 */
trait ForbidDynamicProperties
{
    /**
     * Backtrace limit.
     *
     * To get all the information we need, we only need three frames of the backtrace:
     * [0] Will contain the call to the `fdpGetBacktrace()` method.
     * [1] Will contain the call to the `__set()` method + the class in which the `__set()` is composed in.
     * [2] Will contain the originating context.
     *
     * @var int
     */
    private $fdpBacktraceLimit = 3;

    /**
     * Position in the backtrace of the frame for the call to __set().
     *
     * @var int
     */
    private $fdpSetFrame = 1;

    /**
     * Position in the backtrace of the frame containing the information on the context
     * which triggered the call to __set().
     *
     * @var int
     */
    private $fdpOriginFrame = 2;

    /**
     * Reflection instance of the current property or false if not reflection instance could be created.
     *
     * @var ReflectionProperty|false
     */
    private $fdpReflectionProp;

    /**
     * Magic method which handles property setting for inaccessible and unset properties,
     * but forbids setting non-existent properties.
     *
     * @param string $name  Property name.
     * @param mixed  $value The new value for the property.
     *
     * @return void
     *
     * @throws Error                When an attempt is made to set an inaccessible property.
     * @throws OutOfBoundsException When an attempt is made to set a dynamic property.
     */
    public function __set($name, $value)
    {
        $this->fdpSetReflectionProp($name);

        if ($this->fdpIsPublicProperty()) {
            // This is an unset public property, just set it.
            $this->$name = $value;
            return;
        }

        $backtrace  = $this->fdpGetBacktrace();
        $selfParent = $this->fdpGetParentClass($backtrace);
        $callOrigin = $this->fdpGetCallOrigin($backtrace);

        /*
         * Handle calls which originate from within the class hierarchy which contains the trait.
         */
        if (\is_a($callOrigin, $selfParent, true)) {
            if ($this->fdpIsProtectedProperty()) {
                $this->$name = $value;
                return;
            }

            // This must be a private property.
            if (\property_exists($callOrigin, $name)) {
                if ($callOrigin === $selfParent) {
                    // This is an unset private property accessible from the current context.
                    $this->$name = $value;
                    return;
                }

                /*
                 * Property is not accessible from the current context, use Reflection to set the value,
                 * but make sure the set actually succeeded.
                 */
                if ($this->fdpSetInaccessibleProperty($callOrigin, $name, $value) === true) {
                    return;
                }
            }
        }

        if ($callOrigin !== static::class && \property_exists(static::class, $name) === true) {
            // This is an inaccessible property in the "outer" class. Emulate the PHP native error.
            throw new Error(\sprintf('Cannot access private property %s::$%s', static::class, $name));
            return;
        }

        // This is an attempt to set a dynamic property.
        throw new OutOfBoundsException(
            \sprintf(
                'Dynamic properties are not supported for class %s. Property $%s cannot be set.',
                static::class,
                $name
            )
        );
    }

    /**
     * Retrieve a ReflectionProperty instance of the current property and store it.
     *
     * @param string $propertyName Property name.
     *
     * @return void
     */
    private function fdpSetReflectionProp($propertyName)
    {
        try {
            $this->fdpReflectionProp = new ReflectionProperty($this, $propertyName);
        } catch (ReflectionException $e) {
            $this->fdpReflectionProp = false;
        }
    }

    /**
     * Check if the current property is public.
     *
     * @return bool
     */
    private function fdpIsPublicProperty()
    {
        return $this->fdpReflectionProp !== false && $this->fdpReflectionProp->isPublic();
    }

    /**
     * Check if the current property is protected.
     *
     * @return bool
     */
    private function fdpIsProtectedProperty()
    {
        return $this->fdpReflectionProp !== false && $this->fdpReflectionProp->isProtected();
    }

    /**
     * Retrieve a limited backtrace of the call triggering the `__set()`.
     *
     * @return array[]
     */
    private function fdpGetBacktrace()
    {
        return \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, $this->fdpBacktraceLimit);
    }

    /**
     * Retrieve the fully qualified name of the ultimate parent class in the class hierarchy which contains this trait.
     *
     * @param array $backtrace Backtrace of the call triggering the `__set()`.
     *
     * @return string Class name or an empty string if the class name could not be determined.
     */
    private function fdpGetParentClass($backtrace)
    {
        if (
            isset($backtrace[$this->fdpSetFrame]['class'], $backtrace[$this->fdpSetFrame]['function'])
            && $backtrace[$this->fdpSetFrame]['function'] === '__set'
        ) {
            for ($class = $backtrace[$this->fdpSetFrame]['class']; ($parent = get_parent_class($class)) !== false; $class = $parent);
            return $class;
        }

        // Parent could not be determined. Shouldn't be possible.
        return ''; // @codeCoverageIgnore
    }

    /**
     * Retrieve the fully qualified name of the class containing the code which triggered the call to `__set()`.
     *
     * @param array $backtrace Backtrace of the call triggering the `__set()`.
     *
     * @return string Fully qualified class name or an empty string if the class name
     *                could not be determined or the call was not made from a class context.
     */
    private function fdpGetCallOrigin($backtrace)
    {
        if (isset($backtrace[$this->fdpOriginFrame]['class'])) {
            return $backtrace[$this->fdpOriginFrame]['class'];
        }

        return '';
    }

    /**
     * Set a property on the current object, but for a class which is not accessible
     * from this trait, i.e. a `private` property in another class in the class hierarchy.
     *
     * @param string $targetClass  The class on which the property should to be set.
     * @param string $propertyName Name of the property to set.
     * @param mixed  $value        The new value for the property.
     *
     * @return bool Whether the property was succesfully set.
     */
    private function fdpSetInaccessibleProperty($targetClass, $propertyName, $value)
    {
        try {
            $reflectionProp = new ReflectionProperty($targetClass, $propertyName);
            $reflectionProp->setAccessible(true);
            $reflectionProp->setValue($this, $value);
            $reflectionProp->setAccessible(false);
            return true;
        } catch (ReflectionException $e) {
            return false;
        }
    }
}
