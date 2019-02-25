<?php

namespace Haijin\Persistency\Errors\Query_Expressions;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when a pagination offset expression is used in a query but the limit expression
 * is missing.
 */
class Missing_Limit_Expression_Error extends Persistency_Error
{
}