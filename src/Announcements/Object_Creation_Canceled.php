<?php

namespace Haijin\Persistency\Announcements;

class Object_Creation_Canceled extends Execution_Canceled_Statement
{
    /// Displaying

    public function print_string()
    {
        return $this->get_announcer_print_string() . ' canceled the creation of an object ' .
            get_class( $this->object ) . '.';
    }
}