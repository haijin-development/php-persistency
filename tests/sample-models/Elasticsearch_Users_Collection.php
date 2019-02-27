<?php

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Elasticsearch_Users_Persisted_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "_id" )
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );
        };

    }
}

class Elasticsearch_Users_Collection
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

Elasticsearch_Users_Collection::$instance =
    new Elasticsearch_Users_Persisted_Collection();