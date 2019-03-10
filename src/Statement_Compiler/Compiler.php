<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Expressions\Expressions_Factory_Trait;

class Compiler
{
    use Expressions_Factory_Trait;

    protected $compiled_statement;
    protected $compiler;

    /// Initializing

    public function __construct()
    {
        $this->compiled_statement = null;
        $this->compiler = null;
    }

    /// Compiling

    public function compile($callable)
    {
        $this->compiled_statement = null;
        $this->compiler = null;

        return $this->eval( $callable );
    }

    public function eval($callable)
    {
        $callable( $this );

        return $this->compiled_statement;
    }

    public function get_compiled_statement()
    {
        return $this->compiled_statement;
    }

    /// DSL

    public function query($query_callable)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_callable );

            return;
        }

        $this->compiler = $this->new_query_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $query_callable );

    }

    public function create($create_callable)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_callable );

            return;
        }

        $this->compiler = $this->new_create_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $create_callable );
    }

    public function update($update_callable)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_callable );

            return;
        }

        $this->compiler = $this->new_update_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $update_callable );
    }

    public function delete($delete_callable)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_callable );

            return;
        }

        $this->compiler = $this->new_delete_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $delete_callable );
    }

    /// Instantiating

    protected function new_query_statement_compiler()
    {
        return Create::object( Query_Statement_Compiler::class);
    }

    protected function new_create_statement_compiler()
    {
        return Create::object( Create_Statement_Compiler::class);
    }

    protected function new_update_statement_compiler()
    {
        return Create::object( Update_Statement_Compiler::class);
    }

    protected function new_delete_statement_compiler()
    {
        return Create::object( Delete_Statement_Compiler::class);
    }
}