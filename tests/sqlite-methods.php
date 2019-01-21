<?php

class Sqlite_Methods
{
    static public function add_to($spec)
    {

        $spec->let( "sqlite_file", function() {

            return "test.db";

        });

        $spec->let( "sqlite", function() {

            return new \Sqlite3( $this->sqlite_file );

        });

        $spec->def( "setup_sqlite", function() {

            $this->drop_sqlite_tables();
            $this->create_sqlite_tables();
            $this->populate_sqlite_tables();

        });

        $spec->def( "tear_down_sqlite", function() {

            $this->drop_sqlite_tables();
            $this->sqlite->close();

            if( file_exists( $this->sqlite_file ) ) {
                unlink( $this->sqlite_file );
            }

        });

        $spec->def( "drop_sqlite_tables", function() {

            $this->sqlite->query( "DROP TABLE IF EXISTS users;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS address_1;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS address_2;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS cities;" );

        });

        $spec->def( "create_sqlite_tables", function() {

            $this->sqlite->query(
                "CREATE TABLE `users` (
                    `id` INT PRIMARY KEY,
                    `name` VARCHAR(45) NULL,
                    `last_name` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `address_1` (
                    `id` INT PRIMARY KEY,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `address_2` (
                    `id` INT PRIMARY KEY,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `cities` (
                    `id` INT PRIMARY KEY,
                    `name` VARCHAR(45) NULL
                );"
            );

        });


        $spec->def( "clear_sqlite_tables", function() {

            $this->sqlite->query( "delete from users where 1 = 1;" );
            $this->sqlite->query( "delete from address_1 where 1 = 1;" );
            $this->sqlite->query( "delete from address_2 where 1 = 1;" );
            $this->sqlite->query( "delete from cities where 1 = 1;" );

        });

        $spec->def( "populate_sqlite_tables", function() {

            $this->sqlite->query(
                "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
            );
            $this->sqlite->query(
                "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson' );"
            );
            $this->sqlite->query(
                "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
            );
            $this->sqlite->query(
                "INSERT INTO address_1 VALUES ( 10, 1, 2, 'Evergreen', '742' );"
            );
            $this->sqlite->query(
                "INSERT INTO address_1 VALUES ( 20, 2, 1, 'Evergreen', '742' );"
            );
            $this->sqlite->query(
                "INSERT INTO address_1 VALUES ( 30, 3, 1, 'Evergreen', '742' );"
            );

            $this->sqlite->query(
                "INSERT INTO address_2 VALUES ( 100, 1, 1, 'Evergreen 742', '' );"
            );
            $this->sqlite->query(
                "INSERT INTO address_2 VALUES ( 200, 2, 1, 'Evergreen 742', '' );"
            );
            $this->sqlite->query(
                "INSERT INTO address_2 VALUES ( 300, 3, 1, 'Evergreen 742', '' );"
            );

            $this->sqlite->query(
                "INSERT INTO cities VALUES ( 1, 'Springfield' );"
            );

            $this->sqlite->query(
                "INSERT INTO cities VALUES ( 2, 'Springfield_' );"
            );

        });

    }

}