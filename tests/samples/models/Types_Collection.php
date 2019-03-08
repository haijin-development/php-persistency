<?php

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Types_Persistent_Collection extends Persistent_Collection
{
    public function definition($collection)
    {
        $collection->database = null;

        $collection->collection_name = "types";

        $collection->instantiate_objects_with = \Record_With_Types::class;

        $collection->field_mappings = function($mapping) {

            $mapping->field( "id" ) ->is_primary_key()
                ->type( "integer" )
                ->read_with( "->id" )
                ->write_with( "->id" );

            $mapping->field( "no_type_field" )
                ->read_with( "->no_type_field" )
                ->write_with( "->no_type_field" );

            $mapping->field( "string_field" )
                ->type( "string" )
                ->read_with( "->string_field" )
                ->write_with( "->string_field" );

            $mapping->field( "integer_field" )
                ->type( "integer" )
                ->read_with( "->integer_field" )
                ->write_with( "->integer_field" );

            $mapping->field( "double_field" )
                ->type( "double" )
                ->read_with( "->double_field" )
                ->write_with( "->double_field" );

            $mapping->field( "boolean_field" )
                ->type( "boolean" )
                ->read_with( "->boolean_field" )
                ->write_with( "->boolean_field" );

            $mapping->field( "date_field" )
                ->type( "date" )
                ->read_with( "->date_field" )
                ->write_with( "->date_field" );

            $mapping->field( "time_field" )
                ->type( "time" )
                ->read_with( "->time_field" )
                ->write_with( "->time_field" );

            $mapping->field( "date_time_field" )
                ->type( "date_time" )
                ->read_with( "->date_time_field" )
                ->write_with( "->date_time_field" );

            $mapping->field( "json_field" )
                ->type( "json" )
                ->read_with( "->json_field" )
                ->write_with( "->json_field" );

        };
    }
}

class Types_Collection
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

Types_Collection::$instance = new Types_Persistent_Collection();