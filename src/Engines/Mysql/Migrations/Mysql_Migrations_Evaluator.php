<?php

namespace Haijin\Persistency\Engines\Mysql\Migrations;

use Haijin\Persistency\Migrations\Migrations_Evaluator;

class Mysql_Migrations_Evaluator extends Migrations_Evaluator
{
    public function exists_migrations_table()
    {
        $result = $this->database->execute_sql_string(
            "SHOW TABLES;"
        );

        return count( $result ) > 0
            &&
            in_array( $this->migrations_table_name, array_values( $result[ 0 ] ) );
    }

    public function drop_all()
    {
        foreach( $this->get_all_tables_in_database() as $table ) {

            echo "Dropping table $table ...";

            $this->database->evaluate_sql_string(
                "DROP TABLE IF EXISTS `{$table}`;"
            );
        
            echo "ok.\n";
        }
    }

    public function drop_migrations_table()
    {
        foreach( $this->get_all_tables_in_database() as $table ) {

            echo "Dropping table $table ...";

            $this->database->evaluate_sql_string(
                "DROP TABLE IF EXISTS `{$table}`;"
            );
        
            echo "ok.\n";
        }

    }

    public function create_migrations_table()
    {
        $this->database->evaluate_sql_string(
            "CREATE TABLE `{$this->migrations_table_name}` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `migration_name` VARCHAR(1024) NOT NULL,
                `migration_run_at` TIMESTAMP NOT NULL,
                `source_filename` VARCHAR(1024) NOT NULL,
                PRIMARY KEY (`id`)
            );"
        );
    }

    protected function get_all_tables_in_database()
    {
        $result = $this->database->execute_sql_string(
            "SHOW TABLES;"
        );

        if( count( $result ) == 0 ) {
            return [];
        }

        return array_map( function($row){ return array_values( $row )[ 0 ]; }, $result );
    }
}