<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Persistency\Persistent_Collection\Persistent_Collection;

class Elasticsearch_Persistent_Collection extends Persistent_Collection
{
    /// Querying

    public function find_by_id($id)
    {
        return $this->record_to_object(
            $this->get_database()->find_by_id( $id, $this->collection_name )
        );
    }

    public function find_by($field_values)
    {
        $found_objects = $this->all( function($query) use($field_values) {

            $matches = [];

            foreach( $field_values as $field_name => $value ) {
                $matches[] = $query->match( $field_name, $value );
            }

            $query->filter(
                $query->bool(
                    $query->must( ...$matches )
                )
            );

        });

        $found_count = count( $found_objects );

        if( $found_count == 0 ) {
            return null;
        }

        if( $found_count == 1 ) {
            return $found_objects[ 0 ];
        }

        $this->raise_more_than_one_record_found_error( $found_count );
    }

    public function find_all_by_ids($ids_collection)
    {
        return $this->all( function($query) use($ids_collection) {

            $query->filter(
                $query->ids( 'values', $ids_collection )
            );

        });
    }

    /**
     * Returns the first object in the collection or null if there is none.
     */
    public function first($filter_closure = null, $named_parameters = [], $binding = null)
    {
        if( $filter_closure === null ) {

            $filter_closure = function($query) {

                $query->order_by(
                    $query ->field( '_uid' )
                );

            };

        }

        return parent::first( $filter_closure, $named_parameters, $binding );
    }

    /**
     * Returns the first object in the collection or null if there is none.
     */
    public function all($filter_closure = null, $named_parameters = [], $binding = null)
    {
        if( $filter_closure === null ) {

            $filter_closure = function($query) {

                $query->order_by(
                    $query ->field( '_uid' )
                );

            };

        }

        return parent::all( $filter_closure, $named_parameters, $binding );
    }

    /**
     * Returns the first object in the collection or null if there is none.
     */
    public function last()
    {
        return parent::first( function($query) {

            $query->order_by(
                $query ->field( '_uid' ) ->desc()
            );

        });
    }

    /// Updating

    public function update($object)
    {
        $record_values = $this->get_object_values_from( $object );

        $this->get_database()->update_by_id(
            $this->get_id_of( $object ),
            $record_values,
            $this->collection_name
        );

        return $object;
    }

    /// Deleting

    public function delete($object)
    {
        $this->get_database()->delete_by_id(
            $this->get_id_of( $object ),
            $this->collection_name
        );

        return $object;
    }
}