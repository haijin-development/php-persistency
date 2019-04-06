<?php

namespace Haijin\Persistency\Populate_Scripts;

use Haijin\Instantiator\Create;

class Populate_Scripts_Collection
{
    protected $id;
    protected $name;
    protected $scripts;
    protected $source_filename;

    /// Initializing

    public function __construct()
    {
        $this->id = null;
        $this->name = null;
        $this->scripts = [];
        $this->source_filename = null;
    }

    /// Accessing

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function get_source_filename()
    {
        return $this->source_filename;
    }

    public function set_source_filename($filename)
    {
        $this->source_filename = $filename;

        return $this;
    }

    public function get_scripts()
    {
        return $this->scripts;
    }

    public function add_script($script)
    {
        $this->scripts[] = $script;

        return $this;
    }

    /// Definition

    public function definition($callable)
    {
        $callable( $this );
    }

    public function define_in_file($filename)
    {
        $populate_scripts = $this;

        require( $filename );

        $populate_scripts->set_source_filename( $filename->to_string() );
    }

    public function describe($description, $script_callable)
    {
        $this->add_script(
            new Populate_Script( $description, $script_callable )
        );
    }
}
