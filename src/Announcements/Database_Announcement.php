<?php

namespace Haijin\Persistency\Announcements;

class Database_Announcement extends Persistency_Announcement
{
    /// Accessing

    public function get_database_class()
    {
        return get_class( $this->announcer );
    }
}