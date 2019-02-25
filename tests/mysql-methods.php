<?php

class Mysql_Methods
{
    static public function add_to($spec)
    {

        $spec->let( "mysql", function() {

            return new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        });

        $spec->def( "setup_mysql", function() {

            $this->drop_mysql_tables();
            $this->create_mysql_tables();
            $this->populate_mysql_tables();

        });

        $spec->def( "clear_mysql_tables", function() {

            $this->mysql->query( "TRUNCATE users;" );

        });

        $spec->def( "tear_down_mysql", function() {

            $this->drop_mysql_tables();
            $this->mysql->close();

        });

        $spec->def( "drop_mysql_tables", function() {

            $this->mysql->query( "DROP TABLE IF EXISTS users_read_only;" );
            $this->mysql->query( "DROP TABLE IF EXISTS address_1;" );
            $this->mysql->query( "DROP TABLE IF EXISTS address_2;" );
            $this->mysql->query( "DROP TABLE IF EXISTS cities;" );

            $this->mysql->query( "DROP TABLE IF EXISTS users;" );
        });

        $spec->def( "create_mysql_tables", function() {

            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`users_read_only` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(45) NULL,
                    `last_name` VARCHAR(45) NULL,
                    PRIMARY KEY (`id`)
                );"
            );
            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`address_1` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL,
                    PRIMARY KEY (`id`)
                );"
            );
            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`address_2` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL,
                    PRIMARY KEY (`id`)
                );"
            );
            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`cities` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(45) NULL,
                    PRIMARY KEY (`id`)
                );"
            );

            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`users` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(45) NULL,
                    `last_name` VARCHAR(45) NULL,
                    PRIMARY KEY (`id`)
                );"
            );

        });

        $spec->def( "populate_mysql_tables", function() {

            $this->mysql->query(
                "INSERT INTO users_read_only VALUES ( 1, 'Lisa', 'Simpson' );"
            );
            $this->mysql->query(
                "INSERT INTO users_read_only VALUES ( 2, 'Bart', 'Simpson' );"
            );
            $this->mysql->query(
                "INSERT INTO users_read_only VALUES ( 3, 'Maggie', 'Simpson' );"
            );
            $this->mysql->query(
                "INSERT INTO address_1 VALUES ( 10, 1, 2, 'Evergreen', '742' );"
            );
            $this->mysql->query(
                "INSERT INTO address_1 VALUES ( 20, 2, 1, 'Evergreen', '742' );"
            );
            $this->mysql->query(
                "INSERT INTO address_1 VALUES ( 30, 3, 1, 'Evergreen', '742' );"
            );

            $this->mysql->query(
                "INSERT INTO address_2 VALUES ( 100, 1, 1, 'Evergreen 742', '' );"
            );
            $this->mysql->query(
                "INSERT INTO address_2 VALUES ( 200, 2, 1, 'Evergreen 742', '' );"
            );
            $this->mysql->query(
                "INSERT INTO address_2 VALUES ( 300, 3, 1, 'Evergreen 742', '' );"
            );

            $this->mysql->query(
                "INSERT INTO cities VALUES ( 1, 'Springfield' );"
            );

            $this->mysql->query(
                "INSERT INTO cities VALUES ( 2, 'Springfield_' );"
            );

        });

    }

}