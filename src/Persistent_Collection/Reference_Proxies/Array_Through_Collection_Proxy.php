<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

use Haijin\Ordered_Collection;

class Array_Through_Collection_Proxy extends Reference_Proxy
{
    protected $middle_table;
    protected $left_id_field;
    protected $right_id_field;
    protected $right_collection;

    /// Initializing

    public function __construct(
            $middle_table, $left_id_field, $right_id_field, $right_collection,
            $owner_object, $owner_field, $owners_collection,
            $config
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection, $config );

        $this->middle_table = $middle_table;
        $this->left_id_field = $left_id_field;
        $this->right_id_field = $right_id_field;
        $this->right_collection = $right_collection;
    }

    public function fetch_reference()
    {
        $other_collection_id_field = $this->right_collection->get_id_field();

        $objects = $this->right_collection->all( function($query)
                                                use($other_collection_id_field) {

            $query->join( $this->middle_table )
                        ->from( $other_collection_id_field )
                        ->to( $this->right_id_field ) ->eval( function($query) {

                $query->proyect(
                    $query->ignore()
                );

                $query->join( $this->get_owner_table_name() )
                            ->from( $this->left_id_field )
                            ->to( $this->get_owner_field_id() ) ->eval( function($query) {

                        $query->proyect(
                            $query->ignore()
                        );

                        $query->let( "matches_owner_object_id", function($query) {

                            return $query
                                ->field( $this->get_owner_field_id() )
                                ->op( '=' )
                                ->value( $this->get_owner_object_id() );

                        }, $this );

                }, $this );

            }, $this );

            $query->filter(
                $query->matches_owner_object_id
            );

        }, [], $this );

        return new Ordered_Collection( $objects );
    }

    public function resolve_eager_reference_from($objects_space)
    {
        $all_eager_references = $objects_space->get_all_in_collection(
                $this->right_collection
            );

        $actual_collection = new Ordered_Collection();

        $owner_object_id = $this->get_owner_object_id();

        $back_id_mapping = $this->right_collection->get_primary_key_field_mapping();

        foreach( $all_eager_references as $each_reference ) {

            $back_id = $back_id_mapping->read_value_from( $each_reference );

            if( $owner_object_id == $back_id ) {
                $actual_collection->add( $each_reference );
            }

        }

        $this->resolve_reference_to( $actual_collection );
    }
}