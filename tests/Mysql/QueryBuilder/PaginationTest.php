<?php

namespace Mysql\QueryBuilder\PaginationTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class PaginationTest extends \MysqlQueryTestBase
{
    public function test_offset()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\QueryExpressions\MissingLimitExpressionError::class,
            function() use($database) {
                $database->query( function($query) {

                    $query->collection( "users" );

                    $query->pagination(
                        $query->offset( 2 )
                    );
                });
            },
            function($error) {
                $this->assertEquals(
                    "The 'offset' expression must have a 'limit' expression as well. Please define a '\$query->limit(\$n)' expression.",
                    $error->getMessage()
                );
            }
        );
    }

    public function test_limit()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query->limit( 1 )
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

    public function test_limit_and_offset()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->offset( 1 )
                    ->limit( 1 )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 2,
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }

    public function test_page()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\QueryExpressions\MissingPageSizeExpressionError::class,
            function() use($database) {
                $database->query( function($query) {

                    $query->collection( "users" );

                    $query->pagination(
                        $query->page( 1 )
                    );
                });
            },
            function($error) {
                $this->assertEquals(
                    "The 'page' expression must have a 'page_size' expression as well. Please define a '\$query->page_size(\$n)' expression.",
                    $error->getMessage()
                );
            }
        );
    }

    public function test_page_and_page_size()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->pagination(
                $query
                    ->page( 1 )
                    ->page_size( 1 )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 2,
                    "name" => "Bart",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }
}