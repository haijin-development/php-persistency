<?php

namespace Haijin\Persistency\Factory;

class Create
{
    static public function object($class_name, ...$params)
    {
        return Factory::new( $class_name, ...$params );
    }

    static public function with($class_name)
    {
        return new ObjectInstantiator( $class_name );
    }
}