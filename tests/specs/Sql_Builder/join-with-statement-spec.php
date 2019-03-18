<?php

use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;

$spec->describe( "When building a join with a collection statement", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->xdescribe( "when joining with a reference_to_object field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.address_id = addresses.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

        $this->xit( "builds an aliased join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_id" ) ->as( "a" );

            });

            $expected_sql = 
                "select users.*, a.* " .
                "from users " .
                "left outer join addresses as a on users.address_id = addresses.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->xdescribe( "when joining with a reference_from_object field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_Collection::get() );

                $query->collection( "users" );

                $query->with( "address_2" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->xdescribe( "when joining with a reference_from_collection field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_addresses" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join addresses on users.id = addresses.user_id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

    $this->xdescribe( "when joining with a reference_from_collection field", function() {

        $this->it( "builds a left outer join", function() {

            $sql = $this->query_builder->build( function($query) {

                $query->meta_model( Users_Collection::get() );

                $query->collection( "users" );

                $query->with( "all_indirect_addresses" ) ->eval( function($query) {
                    $query->proyect(
                        $query->ignore()
                    );
                });

            });

            $expected_sql = 
                "select users.* " .
                "from users " .
                "left outer join users_addresses on users.id = users_addresses.user_id " .
                "left outer join addresses on users_addresses.address_id = addresses.id;";

            $this->expect( $sql ) ->to() ->equal( $expected_sql );

        });

    });

});