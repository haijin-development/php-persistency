<?php

use Haijin\Persistency\Sql\Query_Builder\Sql_Query_Statement_Builder;

$spec->describe( "When building a sql expression", function() {

    $this->let( "query_builder", function() {
        return new Sql_Query_Statement_Builder();
    });

    $this->it( "builds a complete sql expression", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {
                $query->proyect(
                    $query->concat(
                        $query->field( "street_name" ), " ", $query->field( "street_number" )
                    ) ->as( "address" )
                );
            });

            $query->filter(
                $query->brackets(
                    $query->brackets(
                        $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                    )
                    ->and()
                    ->brackets(
                        $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                    )
                )
                ->or()
                ->brackets(
                    $query ->field( "address.street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
                )
            );

            $query->order_by(
                $query->field( "users.last_name" ),
                $query->field( "users.name" ),
                $query->field( "address" )
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $expected_sql = "select users.name, users.last_name, concat(address.street_name, ' ', address.street_number) as address" . " " .
            "from users" . " " .
            "join address on users.id = address.user_id" . " " .
            "where ((users.name = 'Lisa') and (users.last_name = 'Simpson')) or (address.street_name like '%Evergreen%')" . " " .
            "order by users.last_name, users.name, address" . " " .
            "limit 10, 0;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

    $this->it( "builds a complete sql expression using macros", function() {

        $sql = $this->query_builder->build( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" ) ->eval( function($query) {

                $query->proyect(
                    $query->concat(
                        $query->field( "street_name" ), " ", $query->field( "street_number" )
                    ) ->as( "address" )
                );

                $query->let( "matches_address", function($query) { return
                    $query->brackets(
                        $query ->field( "street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
                    );
                });

            });

            $query->let( "matches_name", function($query) { return
                $query->brackets(
                    $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                );
            });

            $query->let( "matches_last_name", function($query) { return
                $query->brackets(
                    $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                );
            });

            $query->filter(
                $query->brackets( $query
                    ->matches_name ->and() ->matches_last_name
                )
                ->or()
                ->matches_address
            );

            $query->order_by(
                $query->field( "users.last_name" ),
                $query->field( "users.name" ),
                $query->field( "address" )
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $expected_sql = "select users.name, users.last_name, concat(address.street_name, ' ', address.street_number) as address" . " " .
            "from users" . " " .
            "join address on users.id = address.user_id" . " " .
            "where ((users.name = 'Lisa') and (users.last_name = 'Simpson')) or (address.street_name like '%Evergreen%')" . " " .
            "order by users.last_name, users.name, address" . " " .
            "limit 10, 0;";

        $this->expect( $sql ) ->to() ->equal( $expected_sql );

    });

});