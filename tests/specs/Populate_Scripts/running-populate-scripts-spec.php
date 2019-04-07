<?php

use Haijin\Persistency\CLI\Database_CLI;
use Haijin\Persistency\Populate_Scripts\Populate_Scripts_Evaluator;
use Haijin\Persistency\Engines\Mysql\Mysql_Database;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Errors\Connections\Database_Query_Error;
use Haijin\Errors\File_Not_Found_Error;

$spec->describe( "When running populate scripts in mysql", function() {

    $this->before_all( function() {
        ob_start();
    });

    $this->before_each( function() {
        $mysql_database = ( new Mysql_Database() )
            ->connect( '127.0.0.1', 'haijin', '123456', 'haijin-persistency' );

        Users_Collection::do()->set_database( $mysql_database );

        Users_Collection::do()->clear_all();
    });

    $this->after_all( function() {
        ob_get_clean();

        Users_Collection::do()->clear_all();
    });

    $this->it( "runs the populate scripts the first time", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/one-populate-script-config.php';

        $cli->populate_command();

    });

    $this->it( "runs the populate scripts the second time", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/one-populate-script-config.php';

        $cli->populate_command();

        $cli->populate_command();
    });

    $this->it( "raises an error if the populate scripts folder is missing", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/missing-populate-script-folder-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^Invalid populate scripts folder: 'tests[\/]samples[\/]populate-scripts[\/]missing-populate-script-folder[\/]'[.]$/"
                );
            }

        );

    });

    $this->it( "raises an error if the populate-script id is missing", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/missing-populate-script-id-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The populate script in file 'tests[\/]samples[\/]populate-scripts[\/]missing-populate-script-id[\/]01-create-users.php' is missing its id[.]$/"
                );
            }

        );

    });

    $this->it( "raises an error if the populate script name is missing", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/missing-populate-script-name-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The populate script in file 'tests[\/]samples[\/]populate-scripts[\/]missing-populate-script-name[\/]01-create-users[.]php' is missing its name[.]$/"
                );
            }

        );

    });

    $this->it( "does not rais an error if the populate scripts folder is empty", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/missing-populate-script-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->not() ->to() ->raise( \Exception::class );

    });

    $this->it( "raises an error if a populate-script id is repeated", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/repeated-populate-script-ids-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->to() ->raise(
            Haijin_Error::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->match(
                    "/^The populate script in file 'tests[\/]samples[\/]populate-scripts[\/]repeated-populate-script-ids[\/]02-create-users[.]php' has a repeated unique id[:] '1'[.]$/"
                );
            }

        );

    });

    $this->it( "raises an error with an invalid populate-script script", function() {

        $cli = new Database_CLI( null, new Populate_Scripts_Evaluator() );

        $populate_scripts = $cli->get_populate_scripts_evaluator();

        require 'tests/samples/populate-scripts/invalid-populate-script-config.php';

        $this->expect( function() use($cli) {

            $cli->populate_command();

        }) ->to() ->raise(
            \Exception::class,
            function($error) {
                $this->expect( $error->getMessage() ) ->to() ->equal(
                    'Error from populate script'
                );
            }

        );

    });

});