<?php

namespace ConnectionTest;

require_once __DIR__ . "/MysqlQueryTestBase.php";

use Haijin\Persistency\Mysql\MysqlDatabase;

class ConnectionTest extends \MysqlQueryTestBase
{
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