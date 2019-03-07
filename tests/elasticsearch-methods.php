<?php

use Elasticsearch\ClientBuilder;

class Elasticsearch_Methods
{
    static public function add_to($spec)
    {

        $spec->let( "elasticsearch", function() {

            return ClientBuilder::create()
                ->setHosts( [ '127.0.0.1:9200' ] )
                ->build();

        });

        $spec->def( "setup_elasticsearch", function() {

            $this->drop_elasticsearch_indices();
            $this->create_elasticsearch_indices();
            $this->populate_elasticsearch_indices();

        });

        $spec->def( "clear_elasticsearch_indices", function() {

            if( $this->elasticsearch->indices()->exists([ 'index' => 'users' ]) ) {

                $this->elasticsearch->indices()->delete([ 'index' => 'users' ]);

                $this->elasticsearch->indices()->create([
                    'index' => 'users',
                    'body' => [
                        'mappings' => [
                            'users' => [
                                'properties' => [
                                    'id' => [ 'type' => 'integer' ],
                                    'name' => [ 'type' => 'text', 'fielddata' => true ],
                                    'last_name' => [ 'type' => 'text', 'fielddata' => true ]
                                ]
                            ]
                        ],
                    ]
                ]);

            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'types' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'types' ]);
            }

        });

        $spec->def( "tear_down_elasticsearch", function() {

            $this->drop_elasticsearch_indices();

        });

        $spec->def( "drop_elasticsearch_indices", function() {

            if( $this->elasticsearch->indices()->exists([ 'index' => 'users_read_only' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'users_read_only' ]);
            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'address_1' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'address_1' ]);
            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'address_2' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'address_2' ]);
            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'cities' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'cities' ]);
            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'users' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'users' ]);
            }

            if( $this->elasticsearch->indices()->exists([ 'index' => 'types' ]) ) {
                $this->elasticsearch->indices()->delete([ 'index' => 'types' ]);
            }

        });

        $spec->def( "create_elasticsearch_indices", function() {

            $this->elasticsearch->indices()->create([
                'index' => 'users_read_only',
                'body' => [
                    'mappings' => [
                        'users_read_only' => [
                            'properties' => [
                                'id' => [ 'type' => 'integer' ],
                                'name' => [ 'type' => 'text', 'fielddata' => true ],
                                'last_name' => [ 'type' => 'text', 'fielddata' => true ]
                            ]
                        ]
                    ]
                ]
            ]);

            $this->elasticsearch->indices()->create([
                'index' => 'address_1'
            ]);

            $this->elasticsearch->indices()->create([
                'index' => 'address_2'
            ]);

            $this->elasticsearch->indices()->create([
                'index' => 'cities'
            ]);

            $this->elasticsearch->indices()->create([
                'index' => 'users',
                'body' => [
                    'mappings' => [
                        'users' => [
                            'properties' => [
                                'id' => [ 'type' => 'integer' ],
                                'name' => [ 'type' => 'text' ],
                                'last_name' => [ 'type' => 'text' ]
                            ]
                        ]
                    ],
                ]
            ]);

            $this->elasticsearch->indices()->create([
                'index' => 'types'
            ]);

        });

        $spec->def( "populate_elasticsearch_indices", function() {

            $this->elasticsearch->index([
                'index' => 'users_read_only',
                'type' => 'users_read_only',
                'id' => 1,
                'body' => [
                    'id' => 1,
                    'name' => 'Lisa',
                    'last_name' => 'Simpson'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'users_read_only',
                'type' => 'users_read_only',
                'id' => 2,
                'body' => [
                    'id' => 2,
                    'name' => 'Bart',
                    'last_name' => 'Simpson'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'users_read_only',
                'type' => 'users_read_only',
                'id' => 3,
                'body' => [
                    'id' => 3,
                    'name' => 'Maggie',
                    'last_name' => 'Simpson'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_1',
                'type' => 'address_1',
                'id' => 10,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_1',
                'type' => 'address_1',
                'id' => 20,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_1',
                'type' => 'address_1',
                'id' => 30,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_2',
                'type' => 'address_2',
                'id' => 100,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_2',
                'type' => 'address_2',
                'id' => 200,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'address_2',
                'type' => 'address_2',
                'id' => 300,
                'body' => [
                    'street_name' => 'Evergreen',
                    'street_number' => '742'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'cities',
                'type' => 'cities',
                'id' => 1,
                'body' => [
                    'name' => 'Springfield'
                ],
                'refresh' => true
            ]);

            $this->elasticsearch->index([
                'index' => 'cities',
                'type' => 'cities',
                'id' => 2,
                'body' => [
                    'name' => 'Springfield_'
                ],
                'refresh' => true
            ]);

        });

    }

}