<?php

namespace Haijin\Persistency\Factory;

class SingletonInstantiator
{
    protected $class_name;

    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }

    public function with(...$params)
    {
        $singleton = Create::a( $this->class_name )->with( ...$params );

        GlobalFactory::set_singleton($this->class_name, $singleton );

        return $singleton;
    }
}