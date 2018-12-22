<?php

namespace Mysql\QueryBuilder\FilterTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class IntegrationTest extends \MysqlQueryTestBase
{
    public function test_integration()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) use($database) {

            var_dump( get_class( $query ) );

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );

            $query->join( "address_1" ) ->from( "id" ) ->to( "id_user" )
                ->eval( function($query) {
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
                    $query ->field( "address_1.street_name" ) ->op( "like" )
                        ->value( "%Evergreen%" )
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

            var_dump( $database->sql_string_of( $query ) );
            var_dump( $database->query_parameters_of( $query ) );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function xtest_integration_with_macro_expressions()
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