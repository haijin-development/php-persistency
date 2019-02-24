<?php

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users_read_only";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" )
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

class Users_Collection
{
    static public $instance;

    static public function get()
    {
        return self::$instance;
    }
}

Users_Collection::$instance = new Users_Persistent_Collection();