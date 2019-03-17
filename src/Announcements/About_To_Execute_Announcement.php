<?php

namespace Haijin\Persistency\Announcements;

/**
 * Persistent_Collection can be asked by its listeners to cancel an action.
 * 
 * This library strongly discourages the use of the Observer pattern to extend or
 * change the behaviour of the announcer object. In particular to make the collection
 * to cancel an action, but since it's a common pattern used in other frameworks 
 * we make it possible.
 */
class About_To_Execute_Announcement extends Persistent_Collection_Announcement
{
    protected $canceled;

    /// Initializing

    public function __construct($object)
    {
        parent::__construct($object);

        $this->canceled = false;
        $this->cancelation_reasons = [];
    }

    /// Accessing

    public function get_cancelation_reasons()
    {
        return $this->cancelation_reasons;
    }

    /// Asking

    public function was_canceled()
    {
        return $this->canceled;
    }

    /// Canceling the execution of the action

    public function cancel($reason)
    {
        $this->canceled = true;

        $this->cancelation_reasons[] = $reason;
    }
}