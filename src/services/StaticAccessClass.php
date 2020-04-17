<?php
namespace Services;

abstract class StaticAccessClass {
    protected static $instances = array();

    /**
     *
     * @return self
     */
    public static function get($instanceName = '') {
        if (!isset(static::$instances[get_called_class().'_'.$instanceName])) {
            static::$instances[get_called_class().'_'.$instanceName] = new static();
        }
        return static::$instances[get_called_class().'_'.$instanceName];
    }
}