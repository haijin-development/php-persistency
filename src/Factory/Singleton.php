<?php

namespace Haijin\Persistency\Factory;

class Singleton
{
    static public function create($class_name)
    {
        return new SingletonInstantiator( $class_name );
    }

    static public function of($class_name)
    {
        return GlobalFactory::singleton( $class_name );
    }
}