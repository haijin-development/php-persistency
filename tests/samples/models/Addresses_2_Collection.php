<?php

use Haijin\Persistency\Persistent_Collection\Sql_Persistent_Collection;

require_once __DIR__ . '/Users_Collection.php';

class Addresses_2_Persistent_Collection extends Sql_Persistent_Collection
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
                ->reference_to( Users_Collection::get() )
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

class Addresses_2_Collection
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

Addresses_2_Collection::$instance = new Addresses_2_Persistent_Collection();