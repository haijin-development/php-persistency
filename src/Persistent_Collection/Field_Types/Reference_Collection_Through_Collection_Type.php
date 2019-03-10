<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Persistent_Collection\Reference_Proxies\Array_Through_Collection_Proxy;

class Reference_Collection_Through_Collection_Type extends Abstract_Type
{
    protected $left_collection;
    protected $middle_table;
    protected $left_id_field;
    protected $right_id_field;
    protected $right_collection;
    protected $config;

    /// Initializing

    public function __construct(
            $left_collection,
            $middle_table, $left_id_field, $right_id_field, $right_collection,
            $config
        )
    {
        $this->left_collection = $left_collection;
        $this->middle_table = $middle_table;
        $this->left_id_field = $left_id_field;
        $this->right_id_field = $right_id_field;
        $this->right_collection = $right_collection;
        $this->config = $config;
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

    public function get_referenced_collection()
    {
        return $this->right_collection;
    }

    /// Converting

    public function convert_to_database($object, $database)
    {
        throw new \RuntimeException( "This type is not written into the database." );
    }

    public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        return new Array_Through_Collection_Proxy(
            $this->middle_table,
            $this->left_id_field,
            $this->right_id_field,
            $this->right_collection,
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

        $objects = $this->right_collection->all( function($query) use($ids) {

            $query->join( $this->middle_table )
                        ->from( $this->right_collection->get_id_field() )
                        ->to( $this->right_id_field )
                                                ->eval( function($query) use($ids) {

                $query->proyect(
                    $query->ignore()
                );

                $query->join( $this->left_collection->get_collection_name() )
                            ->from( $this->left_id_field )
                            ->to( $this->left_collection->get_id_field() ) 
                                                ->eval( function($query) use($ids) {

                        $query->proyect(
                            $query->ignore()
                        );

                        $query->let( "matches_owner_object_id",
                                                        function($query) use($ids) {

                            return $query
                                ->field( $this->left_collection->get_id_field() )
                                ->in( $ids );

                        });

                });

            });

            $query->filter(
                $query->matches_owner_object_id
            );

        });

        return $objects;
    }
}