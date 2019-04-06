<?php

namespace Haijin\Persistency\CLI;

use Haijin\Instantiator\Create;
use Clue\Commander\Router;
use Haijin\Persistency\Migrations\Migrations_Builder;

class Database_CLI
{
    protected $migrations_builder;
    protected $mitrations_evaluator;
    protected $environment;

    /// Initializing

    public function __construct()
    {
        $this->migrations_builder = new Migrations_Builder();
        $this->migrations_evaluator = null;
        $this->environment = $this->get_default_env();
    }

    /// Accessing

    public function get_migrations_builder()
    {
        return $this->migrations_builder;
    }

    public function get_running_env()
    {
        return $this->environment;
    }

    public function get_default_env()
    {
        return 'production';
    }

    /// Command line interface

    public function evaluate()
    {
        $router = new Router();

        $router->add( '', function($args) {

            $this->help_command();

            return;

        });

        $router->add( 'drop [<environment>]', function($args) {

            if( isset( $args[ 'environment' ] ) ) {
                $this->environment = $args[ 'environment' ];
            }

            $this->drop_command();

            return;

        });

        $router->add( 'migrate [<environment>]', function($args) {

            if( isset( $args[ 'environment' ] ) ) {
                $this->environment = $args[ 'environment' ];
            }

            $this->migrate_command();

            return;

        });

        $router->add( 'populate [<environment>]', function($args) {

            if( isset( $args[ 'environment' ] ) ) {
                $this->environment = $args[ 'environment' ];
            }

            $this->populate_command();

            return;

        });

        $router->execArgv();
    }

    /// Commands

    public function help_command()
    {
        echo "Usage:\n";
        echo "\n";
        echo "database drop | db drop [test|production]" . "\t" . "Drops all the tables in the environment databases specified in the migrations.php configuration file." . "\n";
        echo "database migrate | db migrate [test|production]" . "\t" . "Runs the pending migrations from the folder specified in the environment migrations.php configuration file." . "\n";
        echo "database populate | db populate [test|production]" . "\t" . "Runs the population scripts from the folder specified in the environment migrations.php configuration file." . "\n";
    }

    public function drop_command()
    {
        $this->migrations_builder->drop_all_databases();

        echo "Dropped ok.\n";
    }

    public function migrate_command()
    {
        $migrations_evaluator = $this->migrations_builder->new_evaluator();

        if( ! $migrations_evaluator->exists_migrations_table() ) {

            $this->initialize_migrations_tables( $migrations_evaluator );

        }

        $migrations_evaluator->run_pending_migrations();
    }

    public function populate_command()
    {
        $populations_evaluator = new Populations_Evaluator();

        $populations_evaluator->run_populations();
    }

    public function initialize_migrations_tables($migrations_evaluator)
    {
        $migrations_evaluator->create_migrations_table();

        echo "Migrations table initialized.\n";
    }
}