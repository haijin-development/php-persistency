<?php

namespace Haijin\Persistency\Announcements;

class About_To_Execute_Elasticsearch_Statement extends About_To_Execute_Statement
{
    protected $endpoint;
    protected $parameters;

    /// Initializing

    public function __construct($endpoint, $parameters)
    {
        parent::__construct();

        $this->endpoint = $endpoint;
        $this->parameters = $parameters;
    }

    /// Accessing

    public function get_endpoint()
    {
        return $this->endpoint;
    }

    public function get_parameters()
    {
        return $this->parameters;
    }

    /// Displaying

    public function print_string()
    {
        $parameters_json = json_encode( $this->parameters );

        return $this->get_database_class() .
            " about to execute: '" . $this->endpoint .
            "' with parameters: '" .$parameters_json . "'";
    }
}