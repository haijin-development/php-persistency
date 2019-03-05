<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

use Haijin\Ordered_Collection;

class Array_From_Collection_Proxy extends Reference_Proxy
{
    protected $persistent_collection;
    protected $id_field;

    /// Initializing

    public function __construct(
            $persistent_collection, $id_field,
            $owner_object, $owner_field, $owners_collection
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection );

        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
    }

    public function fetch_reference()
    {
        $objects = $this->persistent_collection::get()->all( function($query) {

            $query->filter(
                $query
                    ->field( $this->id_field )
                    ->op( "=" )
                    ->value( $this->get_owner_object_id() )
            );

        }, [], $this );

        return new Ordered_Collection( $objects );
    }
}