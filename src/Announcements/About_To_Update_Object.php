<?php

namespace Haijin\Persistency\Announcements;

class About_To_Update_Object extends About_To_Execute_Announcement
{
    /// Displaying

    public function print_string()
    {
        return $this->get_announcer_print_string() . ' about to update an object ' .
            get_class( $this->object ) . '.';
    }
}