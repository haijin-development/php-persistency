<?php

namespace Haijin\Persistency\Announcements;

class Object_Updated extends Persistent_Collection_Announcement
{
    /// Displaying

    public function print_string()
    {
        return $this->get_announcer_print_string() . ' updated an object ' .
            get_class( $this->object ) . '.';
    }
}