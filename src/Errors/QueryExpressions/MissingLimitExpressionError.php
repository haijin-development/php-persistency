<?php

namespace Haijin\Persistency\Errors\QueryExpressions;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when a pagination offset expression is used in a query but the limit expression
 * is missing.
 */
class MissingLimitExpressionError extends PersistencyError
{
}