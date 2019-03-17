<?php

namespace Haijin\Persistency\Announcements;

class Execution_Canceled_Statement extends Persistent_Collection_Announcement
{
    protected $cancelation_reasons;

    /// Initializing

    public function __construct($object, $cancelation_reasons)
    {
        parent::__construct( $object );

        $this->cancelation_reasons = $cancelation_reasons;
    }

    /// Accessing

    public function get_cancelation_reasons()
    {
        return $this->cancelation_reasons;
    }
}