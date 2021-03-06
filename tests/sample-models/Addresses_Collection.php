<?php

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Addresses_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "addresses";

        $collection->instantiate_objects_with = Address::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "user_id" )
                ->read_with( "get_user_id()" )
                ->write_with( "set_user_id()" );

            $mapping->field( "street_1" )
                ->read_with( "get_street_1()" )
                ->write_with( "set_street_1()" );

            $mapping->field( "street_2" )
                ->read_with( "get_street_2()" )
                ->write_with( "set_street_2()" );

            $mapping->field( "city" )
                ->read_with( "get_city()" )
                ->write_with( "set_city()" );
        };

    }
}

class Addresses_Collection
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

Addresses_Collection::$instance = new Addresses_Persistent_Collection();