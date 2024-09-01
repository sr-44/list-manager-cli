<?php

namespace App\Exceptions;

use Exception;

final class ProductNotExistsException extends Exception
{
    protected $message = 'Product does not exists';
}