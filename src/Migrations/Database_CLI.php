<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Instantiator\Create;

class Database_CLI
{
    protected $argv;
    protected $migrations_builder;
    protected $mitrations_evaluator;

    /// Initializing

    public function __construct($argv)
    {
        $this->argv = $argv;
        $this->migrations_builder = new Migrations_Builder();
    }

    /// Configuration

    public function config_in_file($filename)
    {
        $this->migrations_builder->define_in_file( $filename );
    }

    /// Command line interface

    public function evaluate()
    {
        if( count( $this->argv ) == 0 ) {
            return;
        }

        switch( $this->argv[ 1 ] ) {
            case 'drop':
                $this->drop_command();
                break;

            case 'migrate':
                $this->migrate_command();
                break;

            default:
                throw new \RuntimeException( "Uknown command '{$this->argv[1]}'.\n" );
                break;
        }
    }

    /// Commands

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

    public function initialize_migrations_tables($migrations_evaluator)
    {
        $migrations_evaluator->create_migrations_table();

        echo "Migrations table initialized.\n";
    }

    /// Instantiating

    protected function new_migrations_builder()
    {
        return Create::object( Migrations_Builder::class );
    }
}