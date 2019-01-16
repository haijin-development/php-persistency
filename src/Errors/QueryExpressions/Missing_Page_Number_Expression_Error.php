<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when a pagination page_size expression is used in a query but the page expression
 * is missing.
 */
class Missing_Page_Number_Expression_Error extends Persistency_Error
{
}