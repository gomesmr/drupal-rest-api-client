<?php

namespace Drupal\fts\Model;


class Product
{
    public $gtin;
    public $description;
    public $index;
    public $price;
    public $volume;
    public $quantity;
    public $unity;
    public $status;

    public function __construct(array $data) {
        $this->gtin = $data['gtin'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->index = $data['index'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->volume = $data['volume'] ?? null;
        $this->quantity = $data['quantity'] ?? null;
        $this->unity = $data['unity'] ?? null;
        $this->status = $data['status'] ?? null;
    }
}