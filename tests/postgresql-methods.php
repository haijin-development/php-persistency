<?php

class Postgresql_Methods
{
    static public function add_to($spec)
    {

        $spec->let( "postgresql", function() {

            return \pg_connect(
                "host=localhost port=5432 dbname=haijin-persistency user=haijin password=123456"
            );

        });

        $spec->def( "setup_postgresql", function() {

            $this->drop_postgresql_tables();
            $this->create_postgresql_tables();
            $this->populate_postgresql_tables();

        });

        $spec->def( "tear_down_postgresql", function() {

            $this->drop_postgresql_tables();
            pg_close( $this->postgresql );

        });

        $spec->def( "drop_postgresql_tables", function() {

            pg_query( $this->postgresql, "DROP TABLE IF EXISTS users;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS address_1;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS address_2;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS cities;" );

        });

        $spec->def( "create_postgresql_tables", function() {

            pg_query(
                $this->postgresql, 
                "CREATE TABLE users (
                    id INT  PRIMARY KEY,
                    name VARCHAR(45) NULL,
                    last_name VARCHAR(45) NULL
                );"
            );

            pg_query(
                $this->postgresql, 
                "CREATE TABLE address_1 (
                    id INT  PRIMARY KEY,
                    id_user INT NOT NULL,
                    id_city INT NOT NULL,
                    street_name VARCHAR(45) NULL,
                    street_number VARCHAR(45) NULL
                );"
            );
            pg_query(
                $this->postgresql, 
                "CREATE TABLE address_2 (
                    id INT  PRIMARY KEY,
                    id_user INT NOT NULL,
                    id_city INT NOT NULL,
                    street_name VARCHAR(45) NULL,
                    street_number VARCHAR(45) NULL
                );"
            );
            pg_query(
                $this->postgresql, 
                "CREATE TABLE cities (
                    id INT  PRIMARY KEY,
                    name VARCHAR(45) NULL
                );"
            );

        });


        $spec->def( "clear_postgresql_tables", function() {

            pg_query( $this->postgresql, "TRUNCATE users;" );
            pg_query( $this->postgresql, "TRUNCATE address_1;" );
            pg_query( $this->postgresql, "TRUNCATE address_2;" );
            pg_query( $this->postgresql, "TRUNCATE cities;" );

        });

        $spec->def( "populate_postgresql_tables", function() {

            pg_query(
                $this->postgresql, 
                "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
            );

            pg_query(
                $this->postgresql, 
                "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO address_1 VALUES ( 10, 1, 2, 'Evergreen', '742' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO address_1 VALUES ( 20, 2, 1, 'Evergreen', '742' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO address_1 VALUES ( 30, 3, 1, 'Evergreen', '742' );"
            );

            pg_query(
                $this->postgresql, 
                "INSERT INTO address_2 VALUES ( 100, 1, 1, 'Evergreen 742', '' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO address_2 VALUES ( 200, 2, 1, 'Evergreen 742', '' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO address_2 VALUES ( 300, 3, 1, 'Evergreen 742', '' );"
            );

            pg_query(
                $this->postgresql, 
                "INSERT INTO cities VALUES ( 1, 'Springfield' );"
            );

            pg_query(
                $this->postgresql, 
                "INSERT INTO cities VALUES ( 2, 'Springfield_' );"
            );

        });

    }

}