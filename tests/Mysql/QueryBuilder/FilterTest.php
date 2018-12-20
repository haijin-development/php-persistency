<?php

namespace Mysql\QueryBuilder\FilterTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class FilterTest extends \MysqlQueryTestBase
{
    public function test_relative_field_expression()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
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

    public function test_absolute_field_expression()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" ) ->as( "u" );

            $query->filter(
                $query ->field( "u.name" ) ->op( "=" ) ->value( "Lisa" )
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

    public function test_constant_value()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
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

    public function test_function_with_values()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" ) ->op( "=" ) ->concat( "lis", "a" )
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

    public function test_function_nested_functions()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "name" )
                ->op( "=" )
                ->concat( $query->value( "Lis" ), $query->lower("A") )
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

    public function test_binary_operator()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "id" )
                ->op( "=" )
                ->brackets( $query->value( 1 ) ->op( "+" ) ->value( 2 ) )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_is_null_operator()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "id" ) ->is_null()
            );
        });

        $this->assertEquals( [], $rows );
    }

    public function test_is_not_null_operator()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query ->field( "id" ) ->is_not_null()
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

    public function test_unary_function()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" )->upper() ->op( "=" ) ->value( "LISA" )
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

    public function test_brackets()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "id" )
                ->op( "=" )
                ->value( 3 )
                ->op( "*" )
                ->brackets(
                    $query->value( 2 )
                    ->op( "-" )
                    ->value( 1 )
                )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_and()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "last_name" ) ->op( "=" ) ->value( "Simpson" )
                ->and()
                ->field( "name" ) ->op( "=" ) ->value( "Lisa" )
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

    public function test_or()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                ->or()
                ->field( "name" ) ->op( "=" ) ->value( "Maggie" )
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
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_named_parameters()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->filter(
                $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                ->or()
                ->field( "name" ) ->op( "=" ) ->param( "name" )
            );
        },
        [
            "name" => "Maggie"
        ]);

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 1,
                    "name" => "Lisa",
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

    public function test_raises_an_error_when_a_named_parameter_is_not_found()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\Connections\NamedParameterNotFoundError::class,
            function() use($database) {

                $database->query( function($query) {

                    $query->collection( "users" );

                    $query->filter(
                        $query->field( "name" ) ->op( "=" ) ->value( "Lisa" )
                        ->or()
                        ->field( "name" ) ->op( "=" ) ->param( "name" )
                    );
                });
            },
            function($error) {
                $this->assertEquals(
                    "The query named parameter 'name' was not found.",
                    $error->getMessage()
                );

                $this->assertEquals( "name", $error->get_parameter_name() );
            }
        );
    }
}