<?php

namespace Haijin\Persistency\Populate_Scripts;

use Haijin\File_Path;
use Haijin\Errors\Haijin_Error;

class Populate_Scripts_Evaluator
{
    protected $scripts_folder;

    /// Initializing

    public function __construct()
    {
        $this->scripts_folder = null;
    }

    /// Accessing

    public function set_scripts_folder($scripts_folder)
    {
        $this->scripts_folder = $scripts_folder;
    }

    public function get_scripts_folder()
    {
        return $this->scripts_folder;
    }

    /// Configuring

    public function configure($configuration_callable)
    {
        $dsl = new Configuration_DSL( $this );

        $configuration_callable( $dsl );

        return $this;
    } 

    /// Running

    public function run_populate_scripts()
    {
        foreach( $this->get_populate_scripts_to_run() as $populate_script ) {
            $this->run_populate_script( $populate_script );
        }
    }

    public function run_populate_script($populate_scripts_collection)
    {
        echo "Populate script #{$populate_scripts_collection->get_id()} - {$populate_scripts_collection->get_name()}\n";

        foreach( $populate_scripts_collection->get_scripts() as $index => $script ) {
            echo "Running script #{$index}: {$script->get_description()} ...";

            try {

                $script->get_callable()();

                echo " ok.\n";

            } catch( \Exception $e ) {

                echo " failed.\n";

                throw $e;

            }
        }
    }

    /// Searching

    public function get_populate_scripts_to_run()
    {
        if( $this->scripts_folder === null || ! is_dir( $this->scripts_folder ) ) {
            $this->raise_invalid_populate_scripts_folder_error();
        }

        $folder = new File_Path( $this->scripts_folder );

        $all_populate_scripts = [];
        $populate_scripts_ids = [];

        foreach( $folder->get_folder_contents( '*.php' ) as $populate_scripts_file ) {

            $populate_scripts = $this->get_populate_scripts_from_file( $populate_scripts_file );

            if( empty( $populate_scripts->get_id() ) ) {
                $this->raise_missing_id_error( $populate_scripts );
            }

            if( in_array( $populate_scripts->get_id(), $populate_scripts_ids ) ) {
                $this->raise_duplicated_populate_scripts_id_error( $populate_scripts );
            }

            if( empty( $populate_scripts->get_name() ) ) {
                $this->raise_missing_name_error( $populate_scripts );
            }

            if( empty( $populate_scripts->get_scripts() ) ) {
                $this->raise_missing_scripts_error( $populate_scripts );
            }

            $all_populate_scripts[] = $populate_scripts;
            $populate_scripts_ids[] = $populate_scripts->get_id();
        }

        return $all_populate_scripts;
    }

    protected function get_populate_scripts_from_file($filename)
    {
        $populate_scripts = new Populate_Scripts_Collection();

        $populate_scripts->define_in_file( $filename );

        return $populate_scripts;
    }

    protected function raise_invalid_populate_scripts_folder_error()
    {
        throw new Haijin_Error(
            "Invalid populate scripts folder: '$this->scripts_folder'."
        );
    }

    protected function raise_missing_id_error($populate_scripts)
    {
        throw new Haijin_Error(
            "The populate_scripts in file '{$populate_scripts->get_source_filename()}' is missing its id."
        );
    }

    protected function raise_duplicated_populate_scripts_id_error($populate_scripts)
    {
        throw new Haijin_Error(
            "The populate_scripts in file '{$populate_scripts->get_source_filename()}' has a repeated unique id: '{$populate_scripts->get_id()}'."
        );
    }

    protected function raise_missing_name_error($populate_scripts)
    {
        throw new Haijin_Error(
            "The populate_scripts in file '{$populate_scripts->get_source_filename()}' is missing its name."
        );
    }

    protected function raise_missing_scripts_error($populate_scripts)
    {
        throw new Haijin_Error(
            "The populate_scripts in file '{$populate_scripts->get_source_filename()}' has no scripts defined."
        );
    }
}