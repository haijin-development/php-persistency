<?php

namespace Haijin\Persistency\Sql;

use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_Builder;

abstract class Sql_Statement_Builder extends Sql_Expression_Builder
{
     /// Building

     /**
      * Builds and returns a new SQL string.
      *
      * @param callable $expression_callable The callable to build the Query_Statement
      *      using a DSL.
      *
      * @return Query_Statement The built Query_Statement.
      */
     public function build($expression_callable)
     {
         $create_statement = $this->new_statement_compiler()
             ->compile( $expression_callable );

         return $this->build_sql_from( $create_statement );
     }


    /// Creating instances

    abstract protected function new_statement_compiler();
}