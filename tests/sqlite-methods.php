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

        $spec->def( "clear_sqlite_tables", function() {

            $this->sqlite->query( "delete from users;" );

        });

        $spec->def( "tear_down_sqlite", function() {

            $this->drop_sqlite_tables();
            $this->sqlite->close();

            if( file_exists( $this->sqlite_file ) ) {
                unlink( $this->sqlite_file );
            }

        });

        $spec->def( "drop_sqlite_tables", function() {

            $this->sqlite->query( "DROP TABLE IF EXISTS users_read_only;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS address_1;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS address_2;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS cities;" );
            $this->sqlite->query( "DROP TABLE IF EXISTS users;" );

        });

        $spec->def( "create_sqlite_tables", function() {

            $this->sqlite->query(
                "CREATE TABLE `users_read_only` (
                    `id` INTEGER PRIMARY KEY,
                    `name` VARCHAR(45) NULL,
                    `last_name` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `users` (
                    `id` INTEGER PRIMARY KEY,
                    `name` VARCHAR(45) NULL,
                    `last_name` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `address_1` (
                    `id` INTEGER PRIMARY KEY,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `address_2` (
                    `id` INTEGER PRIMARY KEY,
                    `id_user` INT NOT NULL,
                    `id_city` INT NOT NULL,
                    `street_name` VARCHAR(45) NULL,
                    `street_number` VARCHAR(45) NULL
                );"
            );
            $this->sqlite->query(
                "CREATE TABLE `cities` (
                    `id` INTEGER PRIMARY KEY,
                    `name` VARCHAR(45) NULL
                );"
            );

        });


        $spec->def( "populate_sqlite_tables", function() {

            $this->sqlite->query(
                "INSERT INTO users_read_only VALUES ( 1, 'Lisa', 'Simpson' );"
            );
            $this->sqlite->query(
                "INSERT INTO users_read_only VALUES ( 2, 'Bart', 'Simpson' );"
            );
            $this->sqlite->query(
                "INSERT INTO users_read_only VALUES ( 3, 'Maggie', 'Simpson' );"
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