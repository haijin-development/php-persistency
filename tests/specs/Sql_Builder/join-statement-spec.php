<?php

use Haijin\Persistency\Sql\Query_Builder\Sql_Builder;

$spec->describe( "When building the join statement of a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Builder();
    });

    $this->it( "builds a join", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" );

        });

        $expected_sql = 
            "select users.*, address.* " .
            "from users " .
            "join address on users.id = address.user_id;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds an aliased join", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address" ) ->as( "a" ) ->from( "id" ) ->to( "user_id" );

        });

        $expected_sql = 
            "select users.*, a.* " .
            "from users " .
            "join address as a on users.id = a.user_id;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds multiple joins", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address_1" ) ->from( "id" ) ->to( "user_id" );
            $query->join( "address_2" ) ->from( "id" ) ->to( "user_id" );

        });

        $expected_sql = 
            "select users.*, address_1.*, address_2.* " .
            "from users " .
            "join address_1 on users.id = address_1.user_id " .
            "join address_2 on users.id = address_2.user_id;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds join proyections", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {
                $query->proyect(
                    $query->field( "street" ),
                    $query->field( "number" )
                );
            });

        });

        $expected_sql = 
            "select users.name, users.last_name, address.street, address.number " .
            "from users " .
            "join address on users.id = address.user_id;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds macro expressions within join expressions", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {

                $query->let( "matches_street", function($query) {
                    return $query ->field( "street" ) ->op( "=" ) ->value( "Evergreen" );
                });

            });

            $query->filter(
                $query ->matches_street
            );

        });

        $expected_sql = 
            "select users.*, address.* " .
            "from users " .
            "join address on users.id = address.user_id " .
            "where address.street = 'Evergreen';";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds nested joins", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "addresses" ) ->from( "id" ) ->to( "user_id" )->eval( function($query) {
                $query->join( "address" ) ->from( "id" ) ->to( "addresses_id" ) ->eval( function($query) {

                    $query->let( "matches_street", function($query) {
                        return $query ->field( "street" ) ->op( "=" ) ->value( "Evergreen" );
                    });
                });
            });

            $query->filter(
                $query ->matches_street
            );

        });

        $expected_sql = 
            "select users.*, addresses.*, address.* " .
            "from users " .
            "join addresses on users.id = addresses.user_id " .
            "join address on addresses.id = address.addresses_id " .
            "where address.street = 'Evergreen';";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

});