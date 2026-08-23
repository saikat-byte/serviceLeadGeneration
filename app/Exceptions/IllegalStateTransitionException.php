<?php

namespace App\Exceptions;

use Exception;

class IllegalStateTransitionException extends Exception
{
    public static function forEntity($entity, $from, $to)
    {
        $entityName = class_basename($entity);
        return new static("Illegal state transition for {$entityName}: Cannot transition from '{$from}' to '{$to}'.");
    }
}