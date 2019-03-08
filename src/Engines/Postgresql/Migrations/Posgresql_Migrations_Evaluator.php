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
            "DROP TABLE IF EXISTS `{$table_name}`;"
        );
    
        echo "ok.\n";        
    }

    public function get_all_tables_in_database()
    {
        $result = $this->migration_database->execute_sql_string(
            "SELECT * FROM pg_catalog.pg_tables;"
        );

        $result = array_filter( $result, function($table){
            return $table[ 'tableowner' ] == 'public';
        });

        $tables = array_map( function($table){
            return $table[ 'tablename' ];
        }, $result );

        return $tables;
    }

    /// Migrations

    public function create_migrations_table()
    {
        $this->migration_database->evaluate_sql_string(
            "CREATE TABLE `{$this->migrations_table_name}` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `migration_name` VARCHAR(1024) NOT NULL,
                `migration_run_at` TIMESTAMP NOT NULL,
                `source_filename` VARCHAR(1024) NOT NULL,
                PRIMARY KEY (`id`)
            );"
        );
    }
}