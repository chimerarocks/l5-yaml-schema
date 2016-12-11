<?php
namespace ChimeraRocks\YamlSchema\Exceptions;

use Exception;

class InversedEntityNotExistsException extends Exception
{
    public function __construct($entityName = "", $entityInversed = "", $code = 0, Exception $previous = null)
    {
        $message = "The $entityName schema references to an unexisting entity: $entityInversed";
        parent::__construct($message, $code, $previous);
    }
}