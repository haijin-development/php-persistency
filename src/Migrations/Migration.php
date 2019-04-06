<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Instantiator\Create;

class Migration
{
    protected $id;
    protected $name;
    protected $run_at;
    protected $scripts;
    protected $source_filename;

    /// Initializing

    public function __construct()
    {
        $this->id = null;
        $this->name = null;
        $this->run_at = null;
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

    public function get_run_at()
    {
        return $this->run_at;
    }

    public function set_run_at($timestamp)
    {
        $this->run_at = $timestamp;

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

    public function add_script($migration_script)
    {
        $this->scripts[] = $migration_script;

        return $this;
    }

    /// Definition

    public function definition($callable)
    {
        $callable( $this );
    }

    public function define_in_file($filename)
    {
        $migration = $this;

        require( $filename );

        $migration->set_source_filename( $filename->to_string() );
    }

    public function describe($description, $script_callable)
    {
        $this->add_script(
            new Migration_Script( $description, $script_callable )
        );
    }
}
