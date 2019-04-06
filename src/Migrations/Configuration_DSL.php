<?php

namespace Haijin\Persistency\Migrations;

class Configuration_DSL
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

    public function get_database()
    {
        return $this->migrations_builder->get_migration_database();
    }

    public function set_table_name($table_name)
    {
        $this->migrations_builder->set_table_name( $table_name );
    }

    public function get_table_name()
    {
        return $this->migrations_builder->get_table_name();
    }

    public function set_migrated_databases($migrated_databases)
    {
        $this->migrations_builder->set_migrated_databases( $migrated_databases );
    }

    public function get_migrated_databases()
    {
        return $this->migrations_builder->get_migrated_databases();
    }

    public function set_folder($migrations_folder)
    {
        $this->migrations_builder->set_migrations_folder( $migrations_folder );
    }

    public function get_folder()
    {
        return $this->migrations_builder->get_migrations_folder();
    }

    public function __set($attribute_name, $value)
    {
        $setter = "set_{$attribute_name}";

        $this->$setter( $value );
    }

    public function __get($attribute_name)
    {
        $getter = "get_{$attribute_name}";

        return $this->$getter();
    }
}