<?php

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Users_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "users";

        $collection->instantiate_objects_with = User::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->read_with( "get_id()" )
                ->write_with( "set_id()" );

            $mapping->field( "name" )
                ->read_with( "get_name()" )
                ->write_with( "set_name()" );

            $mapping->field( "last_name" )
                ->read_with( "get_last_name()" )
                ->write_with( "set_last_name()" );


            $mapping->field( "address_id" )
                ->reference_to( 'Addresses_Collection' )
                ->read_with( "get_address()" )
                ->write_with( "set_address()" );

            $mapping->field( "address_2" )
                ->reference_from( 'Addresses_Collection', 'user_id' )
                ->write_with( "set_address_2()" );

            $mapping->field( "all_address" )
                ->reference_collection_from( 'Addresses_Collection', 'user_id' )
                ->write_with( "set_all_addresses()" );

            $mapping->field( "all_indirect_address" )
                ->reference_collection_through(
                    'users_addresses', 'user_id', 'address_id', 'Addresses_Collection' )
                ->write_with( "set_all_indirect_addresses()" );

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

    static public function do()
    {
        return self::$instance;
    }
}

Users_Collection::$instance = new Users_Persistent_Collection();