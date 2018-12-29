<?php

namespace Haijin\Persistency\Factory;

class ObjectInstantiator
{
    protected $class_name;

    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }

    public function params(...$params)
    {
        return Factory::new( $this->class_name, ...$params );
    }
}