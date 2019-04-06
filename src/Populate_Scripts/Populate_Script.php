<?php

namespace Haijin\Persistency\Populate_Scripts;

class Populate_Script
{
    protected $description;
    protected $callable;

    /// Initializing

    public function __construct($description, $callable)
    {
        $this->description = $description;
        $this->callable = $callable;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_callable()
    {
        return $this->callable;
    }
}