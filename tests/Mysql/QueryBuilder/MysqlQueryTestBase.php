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
        //self::drop_tables();
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
        $db->query( "DROP TABLE users;" );
        $db->query( "DROP TABLE address_1;" );
        $db->query( "DROP TABLE address_2;" );
        $db->query( "DROP TABLE cities;" );
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
        $db->query(
            "CREATE TABLE `haijin-persistency`.`address_1` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_user` INT NOT NULL,
                `id_city` INT NOT NULL,
                `street_name` VARCHAR(45) NULL,
                `street_number` VARCHAR(45) NULL,
                PRIMARY KEY (`id`)
            );"
        );
        $db->query(
            "CREATE TABLE `haijin-persistency`.`address_2` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_user` INT NOT NULL,
                `id_city` INT NOT NULL,
                `street_name` VARCHAR(45) NULL,
                `street_number` VARCHAR(45) NULL,
                PRIMARY KEY (`id`)
            );"
        );
        $db->query(
            "CREATE TABLE `haijin-persistency`.`cities` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NULL,
                PRIMARY KEY (`id`)
            );"
        );
        $db->close();
    }

    protected function clear_tables()
    {
        $db = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $db->query( "TRUNCATE users;" );
        $db->query( "TRUNCATE address_1;" );
        $db->query( "TRUNCATE address_2;" );
        $db->query( "TRUNCATE cities;" );
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
        $db->query(
            "INSERT INTO address_1 VALUES ( 10, 1, 2, 'Evergreen', '742' );"
        );
        $db->query(
            "INSERT INTO address_1 VALUES ( 20, 2, 1, 'Evergreen', '742' );"
        );
        $db->query(
            "INSERT INTO address_1 VALUES ( 30, 3, 1, 'Evergreen', '742' );"
        );

        $db->query(
            "INSERT INTO address_2 VALUES ( 100, 1, 1, 'Evergreen 742', '' );"
        );
        $db->query(
            "INSERT INTO address_2 VALUES ( 200, 2, 1, 'Evergreen 742', '' );"
        );
        $db->query(
            "INSERT INTO address_2 VALUES ( 300, 3, 1, 'Evergreen 742', '' );"
        );

        $db->query(
            "INSERT INTO cities VALUES ( 1, 'Springfield' );"
        );

        $db->query(
            "INSERT INTO cities VALUES ( 2, 'Springfield_' );"
        );
        $db->close();
    }
}