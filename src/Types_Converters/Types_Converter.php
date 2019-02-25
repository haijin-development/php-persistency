<?php

namespace Haijin\Persistency\Types_Converters;

class Types_Converter
{
    protected $converters;

    /// Initializing

    public function __construct()
    {
        $this->converters = [];

        $this->definition();
    }

    /// Definition

    public function define($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $closure->call( $binding, $this );

        return $this;
    }

    public function definition()
    {
        $this->set_type_converter( "string",  new Null_Converter() );
        $this->set_type_converter( "integer", new Integer_To_String() );
        $this->set_type_converter( "double", new Double_To_String() );
        $this->set_type_converter( "boolean", new Boolean_To_String() );
        $this->set_type_converter( "date",  new DateTime_To_String() );
        $this->set_type_converter( "time", new DateTime_To_String() );
        $this->set_type_converter( "date_time", new DateTime_To_String() );
        $this->set_type_converter( "timestamp", new Null_Converter() );
        $this->set_type_converter( "json", new Json_To_String() );

        return $this;
    }

    public function set_type_converter($type, $converter)
    {
        $this->converters[ $type ] = $converter;
    } 

    /// Converting

    public function convert_from_database($type, $value)
    {
        if( ! isset( $this->converters[ $type ] ) ) {
            throw new \RuntimeException( "Unkown type converter: '$type'" );
        }

        return $this->converters[ $type ]->from_database( $value );
    }

    public function convert_to_database($value, $type = null)
    {
        if( $value === null ) {
            return null;
        }

        if( $type === null ) {

            if( is_a( $value, \Datetime::class ) ) {
                $type = "date_time";
            } elseif( $value === true || $value === false ) {
                $type = "boolean";
            } elseif( is_string( $value ) ) {
                $type = "string";
            } elseif( is_int( $value ) ) {
                $type = "integer";
            } elseif( is_double( $value ) ) {
                $type = "double";
            } elseif( is_array( $value ) ) {
                $type = "json";
            }
        }

        if( ! isset( $this->converters[ $type ] ) ) {
            throw new \RuntimeException( "Unkown type converter: '$type'" );
        }

        return $this->converters[ $type ]->to_database( $value );
    }
}