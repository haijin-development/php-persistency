<?php

namespace Haijin\Persistency\Factory;

use Haijin\Tools\Dictionary;

/**
 * An object to create or obtain instances of objects. A replacement for 'new' with additional 
 * features.
 *
 * This is the beginning of a needed and most necessary refactor.
 * It will be much more light, adaptable and clear than the injection dependecies mechanisms, 
 * allowing to do the same as them but with more easy and in the right places and times and with
 * the right contexts.
 */
class Factory
{
    /// Class methods

    static public $instance;

    static public function instance()
    {
        if( self::$instance === null ) {
            self::$instance = new self();    
        }

        return self::$instance;
    }

    static public function new($class_name, ...$params)
    {
        return self::instance()->_new($class_name, ...$params);
    }

    static public function with_classes_do($closure, $binding)
    {
        return self::instance()->_with_classes_do($closure, $binding);
    }

    /// Instance methods

    public $instantiators;

    public function __construct()
    {
        $this->instantiators = [];
    }

    public function actual_class_for($class_name)
    {
        if( ! array_key_exists( $class_name, $this->instantiators ) ) {
            return $class_name;
        }

        return $this->instantiators[ $class_name ];
    }

    public function _new($class_name, ...$params)
    {
        $class_name_or_closure = $this->actual_class_for($class_name);

        if( is_callable( $class_name_or_closure ) ) {
            return $class_name_or_closure->call( $this, ...$params );
        }

        return new $class_name_or_closure( ...$params );
    }

    public function _with_classes_do($closure, $binding)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $current_instantiators = $this->instantiators;

        try {
            return $closure->call( $binding, $this );
        } finally {
            $this->instantiators = $current_instantiators;
        }
    }

    public function at_put($class_name, $custom_class_name)
    {
        $this->instantiators[ $class_name ] = $custom_class_name;
    }
}