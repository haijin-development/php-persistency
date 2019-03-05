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
            $this->mysql->query( "TRUNCATE addresses;" );
            $this->mysql->query( "TRUNCATE users_addresses;" );
            $this->mysql->query( "TRUNCATE types;" );

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
            $this->mysql->query( "DROP TABLE IF EXISTS addresses;" );
            $this->mysql->query( "DROP TABLE IF EXISTS users_addresses;" );
            $this->mysql->query( "DROP TABLE IF EXISTS types;" );
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
                    `address_id` INT NULL,
                    PRIMARY KEY (`id`)
                );"
            );

            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`addresses` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `user_id` INT NULL,
                    `street_1` VARCHAR(45) NULL,
                    `street_2` VARCHAR(45) NULL,
                    `city` INT NULL,
                    PRIMARY KEY (`id`)
                );"
            );

            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`users_addresses` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `user_id` INT NULL,
                    `address_id` INT NULL,
                    PRIMARY KEY (`id`)
                );"
            );

            $this->mysql->query(
                "CREATE TABLE `haijin-persistency`.`types` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `no_type_field` VARCHAR(45) NULL,
                    `string_field` VARCHAR(45) NULL,
                    `integer_field` INT NULL,
                    `double_field` DOUBLE NULL,
                    `boolean_field` VARCHAR(1) NULL,
                    `date_field` DATE NULL,
                    `time_field` TIME NULL,
                    `date_time_field` DATETIME NULL,
                    `json_field` TEXT NULL,
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