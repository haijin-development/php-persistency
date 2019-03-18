<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Persistent_Collection\Reference_Proxies\Array_From_Collection_Proxy;

class Reference_Collection_From_Collection_Type extends Abstract_Type
{
    protected $persistent_collection;
    protected $id_field;
    protected $config;

    /// Initializing

    public function __construct($persistent_collection, $id_field, $config)
    {
        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
        $this->config = $config;
    }

    /// Accessing

    public function get_referenced_collection()
    {
        return $this->persistent_collection;
    }

    public function get_id_field()
    {
        return $this->id_field;
    }

    /// Asking

    public function references_other_collection()
    {
        return true;
    }

    public function can_write_to_database()
    {
        return false;
    }

    /// Converting

    public function convert_to_database($object, $database)
    {
        throw new Haijin_Error( "This type is not written into the database." );
    }

    public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        return new Array_From_Collection_Proxy(
            $this->persistent_collection,
            $this->id_field,
            $owner_object,
            $owner_field,
            $owners_collection,
            $this->config
        );
    }

    public function fetch_actual_refereces_from_all($proxies)
    {
        $ids = [];

        foreach( $proxies as $proxy ) {

            if( $proxy !== null ) {
                $ids[] = $proxy->get_owner_object_id();
            }
        }

        if( empty( $ids ) ) {
            return [];
        }

        $references = $this->persistent_collection->all( function($query) use($ids) {

            $query->filter(

                $query->field( $this->id_field ) ->in( $ids )

            );

        });

        return $references;
    }

    /// Double dispatch - Building html

    public function build_join_expression_with($sql_builder, $with_expression)
    {
        return $sql_builder->build_collection_from_sql( $with_expression );
    }
}