<?php

namespace Haijin\Persistency\Migrations;

class Migrations_Configuration_DSL
{
    protected $migrations_builder;

    /// Initializing

    public function __construct($migrations_builder)
    {
        $this->migrations_builder = $migrations_builder;
    }

    /// DSL

    public function set_database($database)
    {
        $this->migrations_builder->set_migration_database( $database );
    }

    public function set_table_name($table_name)
    {
        $this->migrations_builder->set_table_name( $table_name );
    }

    public function set_migrated_databases($migrated_databases)
    {
        $this->migrations_builder->set_migrated_databases( $migrated_databases );
    }

    public function set_folder($migrations_folder)
    {
        $this->migrations_builder->set_migrations_folder( $migrations_folder );
    }

    public function __set($attribute_name, $value)
    {
        $setter = "set_{$attribute_name}";

        $this->$setter( $value );
    }

    public function __get($attribute_name)
    {
        $getter = "get_{$attribute_name}";

        $this->$getter();
    }
}