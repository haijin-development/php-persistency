<?php

namespace Haijin\Persistency\Announcements;

class About_To_Execute_Sql_Statement extends About_To_Execute_Statement
{
    protected $sql;
    protected $parameters;

    /// Initializing

    public function __construct($sql, $parameters)
    {
        parent::__construct();

        $this->sql = $sql;
        $this->parameters = $parameters;
    }

    /// Accessing

    public function get_sql()
    {
        return $this->sql;
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
            " about to execute: '" . $this->sql .
            "' with parameters: '" .$parameters_json . "'";
    }
}