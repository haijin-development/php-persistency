<?php

namespace Mysql\QueryBuilder\ConnectionTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class ConnectionTest extends \MysqlQueryTestBase
{
    public function test_raises_an_error_when_the_connection_fails()
    {
        $database = new MysqlDatabase();

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\Connections\ConnectionFailureError::class,
            function() use($database) {
                $database->connect( "127.0.0.1", "", "", "haijin-persistency" );

            },
            function($error) use($database) {
                $this->assertEquals(
                    "mysqli::__construct(): (HY000/1045): Access denied for user ''@'localhost' (using password: NO)",
                    $error->getMessage()
                );

                $this->assertSame( $database, $error->get_database() );
            }
        );
    }

    public function test_raises_an_error_when_the_connection_is_not_initialized()
    {
        $database = new MysqlDatabase();

        $this->expectExactExceptionRaised(
            \Haijin\Persistency\Errors\Connections\UninitializedConnectionError::class,
            function() use($database) {
                $database->query( function($query) {
                    $query->collection( "users" );
                });
            },
            function($error) use($database) {
                $this->assertEquals(
                    'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.',
                    $error->getMessage()
                );

                $this->assertSame( $database, $error->get_database() );
            }
        );
    }
}