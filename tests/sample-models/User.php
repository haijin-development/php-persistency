<?php

class User
{
    protected $id;
    protected $name;
    protected $last_name;

    public function __construct($name = null, $last_name = null)
    {
        $this->id = null;
        $this->name = $name;
        $this->last_name = $last_name;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_last_name()
    {
        return $this->last_name;
    }

    public function set_last_name($last_name)
    {
        $this->last_name = $last_name;
    }
}
