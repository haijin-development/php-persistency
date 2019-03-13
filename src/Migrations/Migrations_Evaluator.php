<?php

namespace Haijin\Persistency\Migrations;

use Haijin\File_Path;
use Haijin\Errors\Haijin_Error;

abstract class Migrations_Evaluator
{
    protected $migration_database;
    protected $migrations_table_name;
    protected $migrations_folder;
    protected $migrated_databases;

    /// Initializing

    public function __construct(
        $migration_database,
        $migrations_table_name,
        $migrations_folder,
        $migrated_databases
    ) {
        $this->migration_database = $migration_database;
        $this->migrations_table_name = $migrations_table_name;
        $this->migrations_folder = $migrations_folder;
        $this->migrated_databases = $migrated_databases;
    }

    /// Asking

    abstract public function exists_table($table_name);

    public function exists_migrations_table()
    {
        return $this->exists_table( $this->migrations_table_name );
    }

    /// Dropping

    abstract public function drop_table($table_name);

    public function drop_all_tables()
    {
        foreach( $this->get_all_tables_in_database() as $table ) {
            $this->drop_table( $table );
        }
    }

    abstract public function get_all_tables_in_database();

    /// Creating

    abstract public function create_migrations_table();

    /// Evaluating migrations

    public function run_pending_migrations()
    {
        foreach( $this->get_pending_migrations() as $migration ) {
            $this->run_migration( $migration );
        }
    }

    public function run_migration($migration)
    {
        echo "Migration #{$migration->get_id()} - {$migration->get_name()}\n";

        foreach( $migration->get_migration_scripts() as $index => $script ) {
            echo "Running script #{$index}: {$script->get_description()} ...";

            try {

                $script->get_callable()( ...$this->migrated_databases );

                echo " ok.\n";

            } catch( \Exception $e ) {

                echo " failed.\n";

                throw $e;

            }
        }

        Migrations_Collection::do()->create( $migration );
    }

    /// Searching

    public function get_pending_migrations()
    {
        $index_of_last_migration_run = $this->get_index_of_last_migration_run();

        $pending_migrations = [];

        foreach( $this->get_all_migrations_from_files() as $migration ) {
            if( $migration->get_id() <= $index_of_last_migration_run ) {
                continue;
            }

            $pending_migrations[] = $migration;
        }

        return $pending_migrations;
    }

    protected function get_index_of_last_migration_run()
    {
        $migration = Migrations_Collection::get()->first( function($query) {

            $query->order_by(
                $query->field( 'id' ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });

        if( $migration === null ) {
            return 0;
        }

        return $migration->get_id();
    }

    protected function get_all_migrations_from_files()
    {
        $folder = new File_Path( $this->migrations_folder );

        $all_migrations = [];
        $migration_ids = [];

        foreach( $folder->get_folder_contents() as $migration_file ) {

            $migration = $this->get_migration_from_file( $migration_file );

            if( in_array( $migration->get_id(), $migration_ids ) ) {
                $this->raise_duplicated_migration_id_error( $migration );
            }

            if( empty( $migration->get_name() ) ) {
                $this->raise_missing_name_error( $migration );
            }

            if( empty( $migration->get_migration_scripts() ) ) {
                $this->raise_missing_scripts_error( $migration );
            }

            $all_migrations[] = $migration;
            $migration_ids[] = $migration->get_id();
        }

        return $all_migrations;
    }

    protected function get_migration_from_file($filename)
    {
        $migration = new Migration();

        $migration->define_in_file( $filename );

        return $migration;
    }

    protected function raise_duplicated_migration_id_error($migration)
    {
        throw new Haijin_Error(
            "The migration in file '{$migration->get_source_filename()}' has a repeated unique id: '{$migration->get_id()}'."
        );
    }

    protected function raise_missing_name_error($migration)
    {
        throw new Haijin_Error(
            "The migration in file '{$migration->get_source_filename()}' is missing its name."
        );
    }

    protected function raise_missing_scripts_error($migration)
    {
        throw new Haijin_Error(
            "The migration in file '{$migration->get_source_filename()}' has no scripts defined."
        );
    }
}