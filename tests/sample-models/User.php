<?php

class User
{
    protected $id;
    protected $name;
    protected $last_name;
    protected $address;
    protected $address_2;
    protected $all_addresses;
    protected $all_indirect_addresses;

    public function __construct($name = null, $last_name = null)
    {
        $this->id = null;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->address = null;
        $this->address_2 = null;
        $this->all_addresses = [];
        $this->all_indirect_addresses = [];
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

    public function get_address()
    {
        return $this->address;
    }

    public function set_address($address)
    {
        $this->address = $address;
    }

    public function get_address_2()
    {
        return $this->address_2;
    }

    public function set_address_2($address)
    {
        $this->address_2 = $address;
    }

    public function get_all_addresses()
    {
        return $this->all_addresses;
    }

    public function set_all_addresses($addresses)
    {
        $this->all_addresses = $addresses;
    }

    public function set_all_indirect_addresses($addresses)
    {
        $this->all_indirect_addresses = $addresses;
    }

    public function get_all_indirect_addresses()
    {
        return $this->all_indirect_addresses;
    }
}
