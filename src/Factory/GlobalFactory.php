<?php

namespace Haijin\Persistency\Factory;

use Haijin\Tools\Dictionary;

/**
 * An global singleton of a Factory.
 */
class GlobalFactory
{
    /// Class methods

    static protected $instance;

    static public function initialize()
    {
        self::$instance = new Factory();    
    }

    static public function new($class_name, ...$params)
    {
        return self::$instance->new($class_name, ...$params);
    }

    static public function singleton($class_name)
    {
        return self::$instance->singleton($class_name);
    }

    static public function set_singleton($class_name, $object)
    {
        return self::$instance->set_singleton($class_name, $object);
    }

    static public function with_classes_do($closure, $binding)
    {
        return self::$instance->with_classes_do($closure, $binding);
    }
}

/**
 * This kind of initialization instead of lazy initialization avoids race conditions and
 * is deterministic.
 */
GlobalFactory::initialize();