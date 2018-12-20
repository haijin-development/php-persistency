<?php

use Haijin\Persistency\Mysql\MysqlDatabase;

class MysqlQueryTestBase extends \PHPUnit\Framework\TestCase
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
        $db->query(
            "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson' );"
        );
        $db->query(
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
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
}