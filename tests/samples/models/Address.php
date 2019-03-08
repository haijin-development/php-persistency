<?php

class Address
{
    protected $id;
    protected $user_id;
    protected $street_1;
    protected $street_2;
    protected $city;
    protected $geo;

    public function __construct()
    {
        $this->id = null;
        $this->user_id = null;
        $this->street_1 = null;
        $this->street_2 = null;
        $this->city = null;
        $this->geo = null;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function get_street_1()
    {
        return $this->street_1;
    }

    public function set_street_1($street_1)
    {
        $this->street_1 = $street_1;
    }

    public function get_street_2()
    {
        return $this->street_2;
    }

    public function set_street_2($street_2)
    {
        $this->street_2 = $street_2;
    }

    public function get_city()
    {
        return $this->city;
    }

    public function set_city($city)
    {
        $this->city = $city;
    }
}
