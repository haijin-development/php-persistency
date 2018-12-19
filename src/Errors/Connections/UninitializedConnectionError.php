<?php

namespace Haijin\Persistency\Errors\Connections;

class UninitializedConnectionError extends \Exception
{
    protected $database;

    /// Initializing

    public function __construct($error_message, $database)
    {
        parent::__construct( $error_message );

        $this->database = $database;
    }

    /// Accessing

    public function get_database()
    {
        return $this->database;
    }
}