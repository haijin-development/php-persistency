<?php

namespace Haijin\Persistency\Engines\Elasticsearch\Migrations;

use Haijin\Persistency\Migrations\Migrations_Evaluator;

class Elasticsearch_Migrations_Evaluator extends Migrations_Evaluator
{
    public function drop_all()
    {
        $this->database->with_handle_do( function($client) {

            foreach( $client->cat()->indices() as $row) {

                $index_name = $row[ 'index' ];

                echo "Dropping index $index_name ...";

                $client->indices()->delete( [ 'index' => $index_name ] );

                echo "ok\n";
            }

        });

    }

    public function exists_migrations_table()
    {
    }

    public function drop_migrations_table()
    {
    }

    public function create_migrations_table()
    {
    }
}