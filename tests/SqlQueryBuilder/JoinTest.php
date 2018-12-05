<?php

namespace Testing\SqlQueryBuilder\SqlQueryBuilderTest;

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
}