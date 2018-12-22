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
                $query->field( "users.last_name" ) ->desc(),
                $query->field( "users.name" ) ->desc(),
                $query->field( "address" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_integration_with_macro_expressions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) use($database) {

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
                $query->field( "users.last_name" ) ->desc(),
                $query->field( "users.name" ) ->desc(),
                $query->field( "address" ) ->desc()
            );

            $query->pagination(
                $query
                    ->offset( 0 )
                    ->limit( 10 )
            );

        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }
}