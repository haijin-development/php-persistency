<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Persistency\Engines\Mysql\Migrations\Mysql_Migrations_Evaluator;
use Haijin\Persistency\Engines\Postgresql\Migrations\Posgresql_Migrations_Evaluator;
use Haijin\Persistency\Engines\Sqlite\Migrations\Sqlite_Migrations_Evaluator;
use Haijin\Persistency\Engines\Elasticsearch\Migrations\Elasticsearch_Migrations_Evaluator;

class Migrations_Builder
{
    protected $migration_database;
    protected $migrated_databases;
    protected $table_name;
    protected $migrations_folder;

    /// Initializing

    public function __construct()
    {
        $this->migration_database = null;
        $this->migrated_databases = [];
        $this->table_name = null;
        $this->migrations_folder = null;
    }

    /// Accessing

    public function set_migration_database($migration_database)
    {
        $this->migration_database = $migration_database;

        Migrations_Collection::do()->set_database( $this->migration_database );
    }

    public function set_migrated_databases($migrated_databases)
    {
        $this->migrated_databases = $migrated_databases;
    }

    public function set_table_name($table_name)
    {
        $this->table_name = $table_name;

        Migrations_Collection::do()->set_collection_name( $this->table_name );
    }

    public function set_migrations_folder($migrations_folder)
    {
        $this->migrations_folder = $migrations_folder;
    }

    /// Configuring

    public function configure($configuration_callable)
    {
        $dsl = new Migrations_Configuration_DSL( $this );

        $configuration_callable( $dsl );

        return $this;
    } 


    /// Instantiating

    public function new_evaluator()
    {
        return $this->new_evaluator_on( $this->migration_database );
    }

    public function new_evaluator_on($database)
    {
        return $database->visit( $this );
    }

    /// Dropping

    public function drop_all_databases()
    {
        foreach( $this->migrated_databases as $database ) {

            $this->new_evaluator_on( $database )
                ->drop_all_tables();

        }
    }

    /// Visiting

    public function accept_mysql_database($database)
    {
        return new Mysql_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->migrations_folder,
            $this->migrated_databases
        );
    }

    public function accept_postgres_database($database)
    {
        return new Posgresql_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->migrations_folder,
            $this->migrated_databases
        );
    }

    public function accept_sqlite_database($database)
    {
        return new Sqlite_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->migrations_folder,
            $this->migrated_databases
        );
    }

    public function accept_elasticsearch_database($database)
    {
        return new Elasticsearch_Migrations_Evaluator(
            $database,
            $this->table_name,
            $this->migrations_folder,
            $this->migrated_databases
        );
    }
}