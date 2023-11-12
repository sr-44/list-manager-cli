<?php

namespace App;

use RuntimeException;

class Actions
{
    private array $lines;

    private string $filename;

    public function __construct(string $filename)
    {
        if (!is_readable($filename)) {
            throw new RuntimeException('Cant open file');
        }
        $this->filename = $filename;
        $this->lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->checkFileSyntax();
    }

    private function checkFileSyntax(): void
    {
        if (!empty($this->lines)) {
            foreach ($this->lines as $line) {
                if (!preg_match('/^.+ - \d+$/u', $line)) {
                    throw new RuntimeException('File syntax is not correct');
                }
            }
        }
    }

    public function add(string $product, int $price): false|int
    {
        if ($this->checkProduct($product)) {
            throw new RuntimeException('Product is already exists');
        }
        return file_put_contents($this->filename, sprintf("%s - %d\n", $product, $price), FILE_APPEND);
    }

    private function checkProduct(string $product): bool
    {
        return str_contains(file_get_contents($this->filename), $product);

    }
}