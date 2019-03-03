<?php

namespace Haijin\Persistency\Migrations;

class Migration_Script
{
    protected $description;
    protected $closure;

    /// Initializing

    public function __construct($description, $closure)
    {
        $this->description = $description;
        $this->closure = $closure;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_closure()
    {
        return $this->closure;
    }
}