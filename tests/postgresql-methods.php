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
            $this->populate_postgresql_read_only_tables();

        });

        $spec->def( "tear_down_postgresql", function() {

            $this->drop_postgresql_tables();
            pg_close( $this->postgresql );

        });

        $spec->def( "drop_postgresql_tables", function() {

            pg_query( $this->postgresql, "DROP TABLE IF EXISTS users_read_only;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS address_1;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS address_2;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS cities;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS users;" );
            pg_query( $this->postgresql, "DROP TABLE IF EXISTS types;" );

        });

        $spec->def( "clear_postgresql_tables", function() {

            pg_query( $this->postgresql, "truncate users restart identity;" );
            pg_query( $this->postgresql, "truncate types restart identity;" );

        });

        $spec->def( "create_postgresql_tables", function() {

            pg_query(
                $this->postgresql, 
                "CREATE TABLE users_read_only (
                    id integer PRIMARY KEY,
                    name varchar(45) NULL,
                    last_name varchar(45) NULL
                );"
            );

            pg_query(
                $this->postgresql, 
                "CREATE TABLE address_1 (
                    id integer PRIMARY KEY,
                    id_user integer NOT NULL,
                    id_city integer NOT NULL,
                    street_name varchar(45) NULL,
                    street_number varchar(45) NULL
                );"
            );
            pg_query(
                $this->postgresql, 
                "CREATE TABLE address_2 (
                    id integer PRIMARY KEY,
                    id_user integer NOT NULL,
                    id_city integer NOT NULL,
                    street_name varchar(45) NULL,
                    street_number varchar(45) NULL
                );"
            );
            pg_query(
                $this->postgresql, 
                "CREATE TABLE cities (
                    id int PRIMARY KEY,
                    name varchar(45) NULL
                );"
            );

            pg_query(
                $this->postgresql, 
                "CREATE TABLE users (
                    id SERIAL PRIMARY KEY,
                    name varchar(45) NULL,
                    last_name varchar(45) NULL,
                    address_id integer NULL
                );"
            );

            pg_query(
                $this->postgresql, 
                "CREATE TABLE types (
                    id SERIAL PRIMARY KEY,
                    no_type_field varchar(45) NULL,
                    string_field varchar(45) NULL,
                    integer_field integer NULL,
                    double_field real NULL,
                    boolean_field boolean NULL,
                    date_field date NULL,
                    time_field time NULL,
                    date_time_field timestamp NULL,
                    timestamp_field timestamp NULL,
                    json_field jsonb NULL
                );"
            );

        });


        $spec->def( "populate_postgresql_read_only_tables", function() {

            pg_query(
                $this->postgresql, 
                "INSERT INTO users_read_only VALUES ( 1, 'Lisa', 'Simpson' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO users_read_only VALUES ( 2, 'Bart', 'Simpson' );"
            );
            pg_query(
                $this->postgresql, 
                "INSERT INTO users_read_only VALUES ( 3, 'Maggie', 'Simpson' );"
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