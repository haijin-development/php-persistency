<?php

namespace Mysql\QueryBuilder\InvalidQueryTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class InvalidQueryTest extends \MysqlQueryTestBase
{
    public function test_raises_an_error_when_the_query_has_an_error()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\Connections\DatabaseQueryError::class,
            function() use($database) {
                $database->query( function($query) {
                    $query->collection( "non_existing_table" );
                });
            },
            function($error) use($database) {
                $this->assertEquals(
                    "Table 'haijin-persistency.non_existing_table' doesn't exist",
                    $error->getMessage()
                );

                $this->assertSame( $database, $error->get_database() );
            }
        );
    }
}