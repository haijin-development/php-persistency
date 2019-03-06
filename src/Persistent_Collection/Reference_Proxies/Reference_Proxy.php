<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

abstract class Reference_Proxy
{
    protected $owner_object;
    protected $owner_field;
    protected $owners_collection;
    protected $config;

    /// Initializing

    public function __construct($owner_object, $owner_field, $owners_collection, $config)
    {
        $this->owner_object = $owner_object;
        $this->owner_field = $owner_field;
        $this->owners_collection = $owners_collection;

        $this->config = $config;
    }

    /// Acessing

    public function get_owner_object()
    {
        return $this->owner_object;
    }

    public function get_owner_field()
    {
        return $this->owner_field;
    }

    public function get_owners_collection()
    {
        return $this->owners_collection;
    }

    public function get_owner_field_mapping()
    {
        return $this->owners_collection->get_field_mapping_at( $this->owner_field );
    }

    public function get_owner_object_id()
    {
        return $this->owners_collection->get_id_of( $this->owner_object );
    }

    public function get_owner_table_name()
    {
        return $this->owners_collection->get_collection_name();
    }

    public function get_owner_field_id()
    {
        return $this->owners_collection->get_id_field();
    }

    /// Resolving reference

    abstract public function fetch_reference();

    public function resolve_reference()
    {
        $fetched_reference = $this->fetch_reference();

        $this->resolve_reference_to( $fetched_reference );

        return $fetched_reference;
    }

    public function resolve_reference_to($actual_object)
    {
        $this->get_owner_field_mapping()->write_value_to(
            $this->owner_object,
            $actual_object,
            null,
            null
        );

        return $actual_object;
    }

    /// Proxy methods

    public function __call($method_name, $params)
    {
        $this->validate_lazy_resolution();

        return $this->resolve_reference()->$method_name( ...$params );
    }

    public function __set($property_name, $value)
    {
        return $this->resolve_reference()->$property_name = $value;
    }

    public function __get($property_name)
    {
        return $this->resolve_reference()->$property_name;
    }

    protected function validate_lazy_resolution()
    {
        if( isset( $this->config[ 'lazy_fetch_warning' ] ) && 
            $this->config[ 'lazy_fetch_warning' ] === true ) {

            $collection_name = get_class( $this->owners_collection );

            trigger_error(
                "The mapping '{$collection_name}.{$this->owner_field}' was lazyly resolved."
            );
        }

        if( isset( $this->config[ 'lazy_fetch_error' ] ) && 
            $this->config[ 'lazy_fetch_error' ] === true ) {

            $collection_name = get_class( $this->owners_collection );

            throw new \RuntimeException(
                "The mapping '{$collection_name}.{$this->owner_field}' was lazyly resolved."
            );
        }
    }
}