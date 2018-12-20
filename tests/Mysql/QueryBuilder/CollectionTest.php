<?php

namespace CollectionTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class CollectionTest extends \MysqlQueryTestBase
{
    public function test_collection_name()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

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

    public function test_collection_alias()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" ) ->as( "c" );

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
}