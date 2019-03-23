<?php

namespace Haijin\Persistency\Engines\Postgresql\Migrations;

use Haijin\Persistency\Migrations\Migrations_Evaluator;

class Posgresql_Migrations_Evaluator extends Migrations_Evaluator
{
    /// Dropping

    public function exists_table($table_name)
    {
        return in_array( $table_name, $this->get_all_tables_in_database() );
    }

    public function drop_table($table_name)
    {
        echo "Dropping Postgresql table $table_name ...";

        $this->migration_database->evaluate_sql_string(
            "DROP TABLE IF EXISTS {$table_name};"
        );
    
        echo "ok.\n";        
    }

    public function get_all_tables_in_database()
    {
        $result = $this->migration_database->execute_sql_string(
            "SELECT table_name FROM information_schema.tables where table_schema = 'public';"
        );

        $tables = array_map( function($table){
            return $table[ 'table_name' ];
        }, $result );

        return $tables;
    }

    /// Migrations

    public function create_migrations_table()
    {
        $this->migration_database->evaluate_sql_string(
            "CREATE TABLE {$this->migrations_table_name} (
                id SERIAL PRIMARY KEY,
                migration_name varchar(1024) NULL,
                migration_run_at timestamp NULL,
                source_filename varchar(1024) NULL
            );"
        );
    }
}