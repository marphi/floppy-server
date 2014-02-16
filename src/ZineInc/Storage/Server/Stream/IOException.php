<?php

namespace ZineInc\Storage\Server\Stream;

use Exception;
use ZineInc\Storage\Server\ErrorCodes;
use ZineInc\Storage\Server\StorageException;

class IOException extends Exception implements StorageException
{
    public function __construct($message = null, Exception $previous = null)
    {
        parent::__construct($message, ErrorCodes::IO, $previous);
    }
}