<?php

namespace Haijin\Persistency\Announcements;

class Persistent_Collection_Announcement extends Persistency_Announcement
{
    protected $object;

    /// Initializing

    public function __construct($object)
    {
        parent::__construct();

        $this->object = $object;
    }

    /// Accessing

    public function get_object()
    {
        return $this->object;
    }
}