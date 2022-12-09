<?php

if (!class_exists('Error')) {

    /**
     * Polyfill the PHP 7.0+ native Error class.
     */
    class Error extends Exception
    {
    }
}
