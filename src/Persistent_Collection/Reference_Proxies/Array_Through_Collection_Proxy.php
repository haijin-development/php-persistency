<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

use Haijin\Ordered_Collection;

class Array_Through_Collection_Proxy extends Reference_Proxy
{
    protected $middle_table;
    protected $left_id_field;
    protected $right_id_field;
    protected $other_collection;

    /// Initializing

    public function __construct(
            $middle_table, $left_id_field, $right_id_field, $other_collection,
            $owner_object, $owner_field, $owners_collection
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection );

        $this->middle_table = $middle_table;
        $this->left_id_field = $left_id_field;
        $this->right_id_field = $right_id_field;
        $this->other_collection = $other_collection;
    }

    public function fetch_reference()
    {
        $other_collection_id_field = $this->other_collection::do()->get_id_field();

        $objects = $this->other_collection::get()->all( function($query)
                                                use($other_collection_id_field) {

            $query->join( $this->middle_table )
                        ->from( $other_collection_id_field )
                        ->to( $this->right_id_field ) ->eval( function($query) {

                $query->join( $this->get_owner_table_name() )
                            ->from( $this->left_id_field )
                            ->to( $this->get_owner_field_id() ) ->eval( function($query) {

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
}