<?php

namespace Haijin\Persistency\Announcements;

class Object_Deleted extends Persistent_Collection_Announcement
{
    /// Displaying

    public function print_string()
    {
        return $this->get_announcer_print_string() . ' deleted an object ' .
            get_class( $this->object ) . '.';
    }
}