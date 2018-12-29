<?php

namespace Haijin\Persistency\Factory;

class Create
{
    static public function object($class_name, ...$params)
    {
        return GlobalFactory::new( $class_name, ...$params );
    }

    static public function a($class_name)
    {
        return new ObjectInstantiator( $class_name );
    }

    static public function an($class_name)
    {
        return new ObjectInstantiator( $class_name );
    }
}