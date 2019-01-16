<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when a pagination page expression is used in a query but the page_size expression
 * is missing.
 */
class Missing_Page_Size_Expression_Error extends Persistency_Error
{
}