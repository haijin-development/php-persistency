<?php

namespace Haijin\Persistency\Migrations;

use Haijin\Instantiator\Create;

class Database_CLI
{
    protected $argv;
    protected $mitrations_evaluator;

    /// Initializing

    public function __construct()
    {
        $this->argv = null;
    }

    /// Command line interface

    public function evaluate($argv)
    {
        $this->argv = $argv;

        if( count( $this->argv ) == 0 ) {
            exit( 0 );
        }

        $app_mode = 'production';
        if( isset( $argv[ 2 ] ) ) {
            $app_mode = $argv[ 2 ];
        }

        var_dump( $app_mode );

        require "src/Config/{$app_mode}/databases.php";

        $migrations_builder = $this->new_migrations_builder()
                        ->define_in_file( "src/Config/{$app_mode}/migrations.php" );

        $this->migrations_evaluator = $migrations_builder->new_evaluator();

        switch( $this->argv[ 1 ] ) {
            case 'drop':
                $this->drop_command( $migrations_builder );
                break;

            case 'migrate':
                $this->migrate_command();
                break;

            default:
                echo "Uknown command '{$this->argv[1]}'.\n";
                exit(1);
                break;
        }

        exit( 0 );
    }

    /// Commands

    public function drop_command($migrations_builder)
    {
        foreach( $migrations_builder->databases_to_drop as $database ) {

            $migrations_builder->new_evaluator_on( $database )->drop_all();

        }

        echo "Dropped ok.\n";
    }

    public function init_command()
    {
        $this->migrations_evaluator->create_migrations_table();

        echo "Migrations table initialized.\n";
    }

    public function migrate_command()
    {
        if( ! $this->migrations_evaluator->exists_migrations_table() ) {
            $this->init_command();
        }

        $this->migrations_evaluator->run_pending_migrations();
    }

    /// Instantiating

    protected function new_migrations_builder()
    {
        return Create::object( Migrations_Builder::class );
    }
}