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
}