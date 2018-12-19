<?php

namespace MysqlQueryTest;

use Haijin\Persistency\Mysql\MysqlDatabase;

/**
 *      CREATE TABLE `haijin-persistency`.`users` (
 *          `id` INT NOT NULL AUTO_INCREMENT,
 *          `name` VARCHAR(45) NULL,
 *          `last_name` VARCHAR(45) NULL,
 *          PRIMARY KEY (`id`)
 *      );
 */
class MysqlQueryTest extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\AllExpectationsTrait;

    public function setUp()
    {
        parent::setUp();

        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "DROP TABLE users IF EXISTS;"
        );
        $db->query(
            "CREATE TABLE `haijin-persistency`.`users` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                PRIMARY KEY (`id`)
            );"
        );
        $db->query(
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
        );
        $db->close();
    }

    public function tearDown()
    {
        parent::tearDown();

        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "DROP TABLE users IF EXISTS;"
        );
        $db->close();
    }

    public function test_collection_name()
    {
        $database = new MysqlDatabase();

        $database->connect( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $rows = $database->query( function($query) {

            $query->collection( "users" );

        });

        $this->expectObjectToBeLike(
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