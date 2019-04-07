<?php

namespace Haijin\Persistency\Persistent_Collection;

class Sql_Persistent_Collection extends Persistent_Collection
{
    /// Searching

    public function exists_with($field_values)
    {
        return $this->exists( function($query) use($field_values) {

            $conditions = $query->ignore();

            foreach( $field_values as $field_name => $value ) {
                $conditions = $conditions ->and() ->brackets(
                    $query->field( $field_name, '=', $value )
                );
            }

            $query->filter( $conditions );

        });
    }

    public function exists($filter_callable)
    {
        return $this->count( $filter_callable ) > 0;
    }

    public function find_all_by_ids(
            $ids_collection, $named_parameters = [], $eager_fetch = []
        )
    {
        $this->validate_named_parameters( $named_parameters );

        return $this->all( function($query) use($ids_collection) {

            $query->filter(
                $query->field( $this->get_id_field() ) ->in( $ids_collection )
            );

        }, $named_parameters, $eager_fetch );
    }

    public function find_by($field_values, $named_parameters = [], $eager_fetch = [])
    {
        $this->validate_named_parameters( $named_parameters );

        $found_objects = $this->all( function($query) use($field_values) {

            $conditions = $query->ignore();

            foreach( $field_values as $field_name => $value ) {

                $conditions = $conditions ->and() ->brackets(
                    $query ->field( $field_name, '=', $value )
                );

            }

            $query->filter( $conditions );

        }, $named_parameters, $eager_fetch );

        $found_count = count( $found_objects );

        if( $found_count == 0 ) {
            return null;
        }

        if( $found_count == 1 ) {
            return $found_objects[ 0 ];
        }

        $this->raise_more_than_one_record_found_error($found_count);
    }

    public function update($object)
    {
        $update_announcement = $this->announce_about_to_update_object( $object );

        if( $update_announcement->was_canceled() ) {

            $this->announce_object_update_canceled(
                $object, $update_announcement->get_cancelation_reasons()
            );

            return;
        }

        $id = $this->get_id_of( $object );
        $record_values = $this->get_record_values_from( $object );

        $this->get_database()->update( function($query) use($id, $record_values) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $id_field = $this->get_id_field();

            $expressions = [];
            foreach( $record_values as $field => $value ) {
                $expressions[] = $query->set( $field, $query->value( $value ) );
            }

            $query->record( ...$expressions );

            $query->filter(
                $query ->field( $id_field, '=', $id )
            );

        });

        $this->announce_object_updated( $object );

        return $object;
    }

    public function delete($object)
    {
        $deletion_announcement = $this->announce_about_to_delete_object( $object );

        if( $deletion_announcement->was_canceled() ) {

            $this->announce_object_deletion_canceled(
                $object, $deletion_announcement->get_cancelation_reasons()
            );

            return;
        }

        $id = $this->get_id_of( $object );

        $this->get_database()->delete( function($query) use($id) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $query->filter(
                $query ->field( $this->get_id_field(), '=', $id )
            );

        }, [] );

        $this->announce_object_deleted( $object );

        return $object;
    }
}
