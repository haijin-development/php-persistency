<?php

use Haijin\Persistency\CLI\Database_CLI;
use Haijin\Persistency\Migrations\Migrations_Builder;

$spec->describe( "When configuring the migrations", function() {

    $this->it( "sets and gets the configuration", function() {

        $migrations_builder = new Migrations_Builder();

        $migrations_builder->configure( function($config) {

            $config->database = 'database';

            $config->table_name = 'table_name';

            $config->migrated_databases = [ 'databases' ];

            $config->folder = 'folder';


            $this->database = $config->database;

            $this->table_name = $config->table_name;

            $this->migrated_databases = $config->migrated_databases;

            $this->folder = $config->folder;

        });

        $this->expect( $this->database ) ->to()
            ->equal( 'database' );

        $this->expect( $this->table_name ) ->to()
            ->equal( 'table_name' );

        $this->expect( $this->migrated_databases ) ->to()
            ->equal( [ 'databases' ] );

        $this->expect( $this->folder ) ->to()
            ->equal( 'folder' );

    });

    $this->it( "configures the Migrations_Builder", function() {

        $migrations_builder = new Migrations_Builder();

        $migrations_builder->configure( function($config) {

            $config->database = 'database';

            $config->table_name = 'table_name';

            $config->migrated_databases = [ 'databases' ];

            $config->folder = 'folder';

        });

        $this->expect( $migrations_builder->get_migration_database() ) ->to()
            ->equal( 'database' );

        $this->expect( $migrations_builder->get_table_name() ) ->to()
            ->equal( 'table_name' );

        $this->expect( $migrations_builder->get_migrated_databases() ) ->to()
            ->equal( [ 'databases' ] );

        $this->expect( $migrations_builder->get_migrations_folder() ) ->to()
            ->equal( 'folder' );

    });

});