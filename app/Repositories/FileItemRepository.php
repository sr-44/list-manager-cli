<?php

namespace App\Repositories;


use App\Exceptions\CannotOpenFileException;
use App\Exceptions\ProductExistsException;
use App\Exceptions\ProductNotExistsException;
use App\Interfaces\ItemRepositoryInterface;
use App\Storage\FileStorage;

class FileItemRepository implements ItemRepositoryInterface
{
    private FileStorage $storage;
    private array $items;

    /**
     * @throws CannotOpenFileException
     */
    public function __construct(FileStorage $storage)
    {
        $this->storage = $storage;
        $this->items = $this->storage->load();
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * @throws ProductExistsException
     */
    public function add(string $name, int $price): void
    {
        if (isset($this->items[$name])) {
            throw new ProductExistsException();
        }
        $this->items[$name] = $price;
        $this->storage->save($this->items);
    }

    /**
     * @throws ProductNotExistsException
     */
    public function update(string $name, int $price): void
    {
        if (!isset($this->items[$name])) {
            throw new ProductNotExistsException();
        }

        $this->items[$name] = $price;
        $this->storage->save($this->items);
    }

    /**
     * @throws ProductNotExistsException
     */
    public function delete(string $name): void
    {
        if (isset($this->items[$name])) {
            unset($this->items[$name]);
            $this->storage->save($this->items);
        } else {
            throw new ProductNotExistsException();
        }
    }

    public function totalSum(): int
    {
        return array_sum($this->items);
    }
}