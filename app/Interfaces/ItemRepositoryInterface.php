<?php

namespace App\Interfaces;

use App\Exceptions\ProductExistsException;
use App\Exceptions\ProductNotExistsException;

interface ItemRepositoryInterface
{
    public function all(): array;

    /**
     * @throws ProductExistsException
     */
    public function add(string $name, int $price): void;

    /**
     * @throws ProductNotExistsException
     */
    public function update(string $name, int $price): void;

    /**
     * @throws ProductNotExistsException
     */
    public function delete(string $name): void;

    public function totalSum(): int;
}
