<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

class Object_From_Collection_Proxy extends Reference_Proxy
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

    /// Resolving reference

    public function fetch_reference()
    {
        return $this->persistent_collection::do()
                    ->find_by( [ $this->id_field => $this->get_owner_object_id() ] );
    }
}