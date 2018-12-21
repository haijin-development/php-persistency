<?php

namespace Mysql\QueryBuilder\FilterMacrosTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class FilterMacrosTest extends \MysqlQueryTestBase
{
    public function test_macro_definition()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->filter( $query
                ->matches_name
            );
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

    public function test_combining_macro_definitions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->let( "matches_name", function($query) { return $query
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" );
            });

            $query->let( "matches_last_name", function($query) { return $query
                ->field( "last_name" ) ->op( "=" ) ->value( "Simpson" );
            });

            $query->filter(
                $query ->matches_name ->and() ->matches_last_name
            );
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
}