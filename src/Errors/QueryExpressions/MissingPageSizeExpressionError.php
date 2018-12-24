<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a pagination page expression is used in a query but the page_size expression
 * is missing.
 */
class MissingPageSizeExpressionError extends PersistencyError
{
}