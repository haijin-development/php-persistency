<?php

namespace Haijin\Persistency\Engines\Elasticsearch\Migrations;

use Haijin\Persistency\Migrations\Migrations_Evaluator;

class Elasticsearch_Migrations_Evaluator extends Migrations_Evaluator
{
    /// Dropping

    public function exists_table($index_name)
    {
        return in_array( $index_name, $this->get_all_tables_in_database() );
    }

    public function drop_table($index_name)
    {
        echo "Dropping Elasticsearch table $index_name ...";

        $this->migration_database->with_handle_do( function($client) use($index_name) {

            $client->indices()->delete( [ 'index' => $index_name ] );

        });

        echo "ok.\n";        
    }

    public function get_all_tables_in_database()
    {
        return $this->migration_database->with_handle_do( function($client) {

            return array_map( function($row){

                return $row[ 'index' ];

            }, $client->cat()->indices() );

        });

    }

    /// Migrations

    public function create_migrations_table()
    {
    }
}