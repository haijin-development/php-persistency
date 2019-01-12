<?php

\Haijin\Specs\Specs_Runner::configure( function($specs) {

    $this->before_all( function() {

        $this->mysql = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );

        $this->drop_tables();
        $this->create_tables();
        $this->populate_tables();

    });

    $this->after_all( function() {

        $this->drop_tables();

        $this->mysql->close();

    });

    $this->def( "drop_tables", function() {

        $this->mysql->query( "DROP TABLE users;" );
        $this->mysql->query( "DROP TABLE address_1;" );
        $this->mysql->query( "DROP TABLE address_2;" );
        $this->mysql->query( "DROP TABLE cities;" );

    });

    $this->def( "create_tables", function() {

        $this->mysql = new \mysqli( "127.0.0.1", "haijin", "123456", "haijin-persistency" );
        $this->mysql->query(
            "CREATE TABLE `haijin-persistency`.`users` (
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

    });


    $this->def( "clear_tables", function() {

        $this->mysql->query( "TRUNCATE users;" );
        $this->mysql->query( "TRUNCATE address_1;" );
        $this->mysql->query( "TRUNCATE address_2;" );
        $this->mysql->query( "TRUNCATE cities;" );

    });

    $this->def( "populate_tables", function() {

        $this->mysql->query(
            "INSERT INTO users VALUES ( 1, 'Lisa', 'Simpson' );"
        );
        $this->mysql->query(
            "INSERT INTO users VALUES ( 2, 'Bart', 'Simpson' );"
        );
        $this->mysql->query(
            "INSERT INTO users VALUES ( 3, 'Maggie', 'Simpson' );"
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

});