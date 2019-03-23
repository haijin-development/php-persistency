<?php

namespace Haijin\Persistency\Engines\Sqlite\Migrations;

use Haijin\Persistency\Migrations\Migrations_Evaluator;

class Sqlite_Migrations_Evaluator extends Migrations_Evaluator
{
    /// Dropping

    public function exists_table($table_name)
    {
        return in_array( $table_name, $this->get_all_tables_in_database() );
    }

    public function drop_table($table_name)
    {
        echo "Dropping Sqlite table $table_name ...";

        $this->migration_database->evaluate_sql_string(
            "DROP TABLE IF EXISTS `{$table_name}`;"
        );
    
        echo "ok.\n";        
    }

    public function get_all_tables_in_database()
    {
        $result = $this->migration_database->execute_sql_string(
            "SELECT * FROM sqlite_master WHERE type = 'table';"
        );

        return array_map( function($row){

            return $row[ 'tbl_name' ];

        }, $result );
    }

    /// Migrations

    public function create_migrations_table()
    {
        $this->migration_database->evaluate_sql_string(
            "CREATE TABLE `{$this->migrations_table_name}` (
                `id` INTEGER PRIMARY KEY,
                `migration_name` VARCHAR(1024) NOT NULL,
                `migration_run_at` TIMESTAMP NOT NULL,
                `source_filename` VARCHAR(1024) NOT NULL
            );"
        );
    }
}