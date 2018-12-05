<?php

namespace Testing\SqlQueryBuilder\SqlQueryBuilderTest;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function test_integration()
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
                    $query->concat(
                        $query->field( "street_name" ), " ", $query->field( "street_number" )
                    ) ->as( "address" )
                );
            });

            $query->filter( $query
                ->brackets( $query
                    ->brackets( $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" ) )
                    ->and()
                    ->brackets( $query ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" ) )
                )
                ->or()
                ->brackets(
                    $query ->field( "address.street_name" ) ->op( "like" ) ->value( "%Evergreen%" )
                )
            );

            $query->order_by(
                $query->field( "last_name" ),
                $query->field( "name" ),
                $query->field( "address.address" )
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
            "order by users.last_name, users.name, address.address" . " " .
            "limit 10, 0;";

        $this->assertEquals( $expected_sql, $sql );
    }
}