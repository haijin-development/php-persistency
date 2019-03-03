<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Persistency\Engines\Mysql\Migrations\Mysql_Migrations_Evaluator;
use Haijin\Persistency\Engines\Elasticsearch\Migrations\Elasticsearch_Migrations_Evaluator;

class Migrations_Builder
{
    public $database;
    public $table_name;
    public $folder;
    public $databases_to_drop;

    /// Definition

    public function define_in_file($filename)
    {
        $migrations = $this;

        require( $filename );

        Migrations_Collection::do()->set_database( $this->database );
        Migrations_Collection::do()->set_collection_name( $this->table_name );

        return $this;
    }

    /// Instantiating

    public function new_evaluator()
    {
        return $this->new_evaluator_on( $this->database );
    }

    public function new_evaluator_on($database)
    {
        return $database->visit( $this );
    }

    /// Visiting

    public function accept_mysql_database($database)
    {
        return new Mysql_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->folder
        );
    }

    public function accept_elasticsearch_database($database)
    {
        return new Elasticsearch_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->folder
        );
    }
}