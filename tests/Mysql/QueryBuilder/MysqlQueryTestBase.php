<?php

use Haijin\Persistency\Mysql\MysqlDatabase;

class MysqlQueryTestBase extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\AllExpectationsTrait;

    static public function setUpBeforeClass()
    {
        self::drop_tables();
        self::create_tables();
    }

    static public function tearDownBeforeClass()
    {
        self::drop_tables();
    }

    public function setUp()
    {
        parent::setUp();

        $this->clear_tables();
        $this->populate_tables();
    }

    static protected function drop_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "DROP TABLE users;"
        );
        $db->close();
    }

    static protected function create_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "CREATE TABLE `haijin-persistency`.`users` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                PRIMARY KEY (`id`)
            );"
        );
        $db->close();
    }

    protected function clear_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query(
            "TRUNCATE users;"
        );
        $db->close();
    }

    protected function populate_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
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
}