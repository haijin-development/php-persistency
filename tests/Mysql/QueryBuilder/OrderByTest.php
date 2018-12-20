<?php

namespace OrderByTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class OrderByTest extends \MysqlQueryTestBase
{
    public function test_order_by_fields()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "last_name" ),
                $query->field( "name" )
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 2,
                    "name" => "Marge",
                    "last_name" => "Bouvier"
                ],
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

    public function test_order_by_desc()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "id" ) ->desc()
            );
        });

        $this->expectObjectToBeExactly(
            $rows,
            [
                [
                    "id" => 3,
                    "name" => "Maggie",
                    "last_name" => "Simpson"
                ],
                [
                    "id" => 2,
                    "name" => "Marge",
                    "last_name" => "Bouvier"
                ],
                [
                    "id" => 1,
                    "name" => "Lisa",
                    "last_name" => "Simpson"
                ]
            ]
        );
    }


    public function test_order_by_asc()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

            $query->order_by(
                $query->field( "name" ) ->asc()
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
                ],
                [
                    "id" => 2,
                    "name" => "Marge",
                    "last_name" => "Bouvier"
                ]
            ]
        );
    }

    protected function populate_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
        );
        $db->query(
            "INSERT INTO users VALUES ( 2, 'Marge', 'Bouvier' );"
        );
        $db->query(
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
        );
        $db->close();
    }
}