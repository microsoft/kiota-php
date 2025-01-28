<?php

namespace Microsoft\Kiota\Serialization\Multipart\Exceptions;

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException
{
    public function __construct(string $methodName = "")
    {
        parent::__construct("The method $methodName is not implemented.");
    }
}