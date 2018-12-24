<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a pagination page_size expression is used in a query but the page expression
 * is missing.
 */
class MissingPageNumberExpressionError extends PersistencyError
{
}