<?php

namespace App\Exceptions;

use Exception;

final class CannotOpenFileException extends Exception
{
    protected $message = 'Cant open file';
}