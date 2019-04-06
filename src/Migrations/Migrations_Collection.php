<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

class Migrations_Persistent_Collection extends Sql_Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = null;

        $collection->instantiate_objects_with = Migration::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "migration_name" )
                ->type( "string" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "migration_run_at" )
                ->type( "date_time" )
                ->read_with( "get_run_at()" )
                ->write_with( "set_run_at()" );

            $mapping->field( "source_filename" )
                ->type( "string" )
                ->read_with( "get_source_filename()" )
                ->write_with( "set_source_filename()" );
        };

    }

    public function create($migration)
    {
        if( $migration->get_run_at() === null ) {
            $migration->set_run_at( new \DateTime() );
        }

        parent::create( $migration );
    }
}

class Migrations_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }

    static public function do()
    {
        return self::$instance;
    }
}

Migrations_Collection::$instance = new Migrations_Persistent_Collection();