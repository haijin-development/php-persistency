<?php

namespace SqlQueryBuilder\JoinTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class JoinTest extends \PHPUnit\Framework\TestCase
{
    public function test_join()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address" ) ->from( "id" ) ->to( "user_id" );
        });

        $expected_sql = 
            "select users.*, address.* " .
            "from users " .
            "join address on users.id = address.user_id;";

        $this->assertEquals( $expected_sql, $sql );
    }

    public function test_aliased_join()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address" ) ->as( "a" ) ->from( "id" ) ->to( "user_id" );
        });

        $expected_sql = 
            "select users.*, a.* " .
            "from users " .
            "join address as a on users.id = a.user_id;";

        $this->assertEquals( $expected_sql, $sql );
    }

    public function test_multiple_joins()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

            $query->collection( "users" );

            $query->join( "address_1" ) ->from( "id" ) ->to( "user_id" );
            $query->join( "address_2" ) ->from( "id" ) ->to( "user_id" );
        });

        $expected_sql = 
            "select users.*, address_1.*, address_2.* " .
            "from users " .
            "join address_1 on users.id = address_1.user_id " .
            "join address_2 on users.id = address_2.user_id;";

        $this->assertEquals( $expected_sql, $sql );
    }

    public function test_join_proyections()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

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

        $this->assertEquals( $expected_sql, $sql );
    }

    public function test_join_macro_expressions()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

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

        $this->assertEquals( $expected_sql, $sql );
    }

    public function test_nested_joins()
    {
        $query_builder = new SqlBuilder();

        $sql = $query_builder->build( function($query) {

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

        $this->assertEquals( $expected_sql, $sql );
    }
}