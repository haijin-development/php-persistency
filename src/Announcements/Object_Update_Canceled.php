<?php

namespace Haijin\Persistency\Announcements;

class Object_Update_Canceled extends Execution_Canceled_Statement
{
    /// Displaying

    public function print_string()
    {
        return $this->get_announcer_print_string() . ' canceled the update of an object ' .
            get_class( $this->object ) . '.';
    }
}