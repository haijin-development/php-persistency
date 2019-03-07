<?php

namespace Haijin\Persistency\Statement_Compiler;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements\Expressions\Expressions_Factory_Trait;

class Compiler
{
    use Expressions_Factory_Trait;

    protected $binding;
    protected $compiled_statement;
    protected $compiler;

    /// Initializing

    public function __construct()
    {
        $this->binding = $this;
        $this->compiled_statement = null;
        $this->compiler = null;
    }

    /// Compiling

    public function compile($closure, $binding = null)
    {
        $this->compiled_statement = null;
        $this->compiler = null;

        return $this->eval( $closure, $binding );
    }

    public function eval($closure, $binding = null)
    {
        if( $binding !== null ) {
            $this->binding = $binding;
        }

        $closure->call( $this->binding, $this );

        return $this->compiled_statement;
    }

    public function get_compiled_statement()
    {
        return $this->compiled_statement;
    }

    /// DSL

    public function query($query_closure)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_closure, $this->binding );

            return;
        }

        $this->compiler = $this->new_query_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $query_closure, $this->binding );

    }

    public function create($create_closure)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_closure, $this->binding );

            return;
        }

        $this->compiler = $this->new_create_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $create_closure, $this->binding );
    }

    public function update($update_closure)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_closure, $this->binding );

            return;
        }

        $this->compiler = $this->new_update_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $update_closure, $this->binding );
    }

    public function delete($delete_closure)
    {
        if( $this->compiler !== null ) {
            $this->compiled_statement =
                $this->compiler->eval( $query_closure, $this->binding );

            return;
        }

        $this->compiler = $this->new_delete_statement_compiler();

        $this->compiled_statement = 
            $this->compiler->compile( $delete_closure, $this->binding );
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