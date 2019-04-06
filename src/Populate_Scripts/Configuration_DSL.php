<?php

namespace Haijin\Persistency\Populate_Scripts;

class Configuration_DSL
{
    protected $populate_scripts_evaluator;

    /// Initializing

    public function __construct($populate_scripts_evaluator)
    {
        $this->populate_scripts_evaluator = $populate_scripts_evaluator;
    }

    /// DSL

    public function set_folder($set_scripts_folder)
    {
        $this->populate_scripts_evaluator->set_scripts_folder( $set_scripts_folder );
    }

    public function get_folder()
    {
        return $this->populate_scripts_evaluator->get_scripts_folder();
    }

    public function __set($attribute_name, $value)
    {
        $setter = "set_{$attribute_name}";

        $this->$setter( $value );
    }

    public function __get($attribute_name)
    {
        $getter = "get_{$attribute_name}";

        return $this->$getter();
    }
}