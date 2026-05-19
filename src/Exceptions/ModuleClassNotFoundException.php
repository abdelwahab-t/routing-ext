<?php

namespace Abdelwahab\RoutingExt\Exceptions;

use Exception;
use Throwable;

class ModuleClassNotFoundException extends Exception
{
    public function __construct(string $class = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Module Class {$class} not found", $code, $previous);
    }
}