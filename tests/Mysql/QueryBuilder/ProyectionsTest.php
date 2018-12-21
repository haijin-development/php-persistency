<?php

namespace Mysql\QueryBuilder\PaginationTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class ProyectionsTest extends \MysqlQueryTestBase
{
    public function test_select_all()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->all()
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ],
                [
                    "id" => 2,
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ],
                [
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_select_fields()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ),
                $query->field( "last_name" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ],
                [
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_select_aliased_fields()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->field( "name" ) ->as( "n" ),
                $query->field( "last_name" ) ->as( "ln" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "n" => "Lisa",
                    "ln" => "Simpson"
                ],
                [
                    "n" => "Bart",
                    "ln" => "Simpson"
                ],
                [
                    "n" => "Maggie",
                    "ln" => "Simpson"
                ]
            ]
        );
    }

    public function test_select_constant_values()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query ->value( 1 ),
                $query ->value( "2" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    1 => 1,
                    2 => "2"
                ],
                [
                    1 => 1,
                    2 => "2"
                ],
                [
                    1 => 1,
                    2 => "2"
                ]
            ]
        );
    }

    public function test_select_aliased_constant_values()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->value( 1 ) ->as( "v1" ),
                $query->value( "2" ) ->as( "v2" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "v1" => 1,
                    "v2" => "2"
                ],
                [
                    "v1" => 1,
                    "v2" => "2"
                ],
                [
                    "v1" => 1,
                    "v2" => "2"
                ]
            ]
        );
    }

    public function test_function_with_values()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->concat( "1", "0" ) ->as( "s" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "s" => "10"
                ],
                [
                    "s" => "10"
                ],
                [
                    "s" => "10"
                ]
            ]
        );
    }

    public function test_function_with_value_expressions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->concat(
                    $query->value( "1" ),
                    $query->value( "0" )
                ) ->as( "s" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "s" => "10"
                ],
                [
                    "s" => "10"
                ],
                [
                    "s" => "10"
                ]
            ]
        );
    }

    public function test_nested_functions_expressions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->concat(
                    $query->upper( "a" ),
                    $query->lower( "A" )
                ) ->as( "s" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "s" => "Aa"
                ],
                [
                    "s" => "Aa"
                ],
                [
                    "s" => "Aa"
                ]
            ]
        );
    }

    public function test_binary_operator()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->proyect(
                $query->brackets(
                    $query->value( 1 ) ->op( "+" ) ->value( 2 )
                ) ->as( "n" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "n" => 3
                ],
                [
                    "n" => 3
                ],
                [
                    "n" => 3
                ]
            ]
        );
    }
}