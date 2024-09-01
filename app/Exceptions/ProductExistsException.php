<?php

namespace App\Exceptions;

use Exception;

final class ProductExistsException extends Exception
{
    protected $message = 'Product is already exists';
}