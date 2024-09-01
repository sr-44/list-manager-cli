<?php

namespace App\Storage;

use App\Exceptions\CannotOpenFileException;

class FileStorage
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws CannotOpenFileException
     */
    public function load(): array
    {
        if (!file_exists($this->filePath)) {
            throw new CannotOpenFileException();
        }

        $items = [];
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            [$name, $price] = explode(' — ', $line);
            $items[$name] = (int)$price;
        }

        return $items;
    }

    public function save(array $items): void
    {
        $lines = [];
        foreach ($items as $name => $price) {
            $lines[] = $name . ' — ' . $price;
        }
        file_put_contents($this->filePath, implode(PHP_EOL, $lines));
    }
}
