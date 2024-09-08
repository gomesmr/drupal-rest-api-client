<?php

namespace Drupal\fts\Model;


class ApiResponse
{
    public $products = [];
    public $company;
    public $user;
    public $dateTime;
    public $recalculateProducts = [];
    public $errorMessage = [];

    public function __construct($response)
    {
        $this->dateTime = $response['dateTime'] ?? null;
        $this->company = new Company();
        $this->company->companyName = $response['company']['companyName'] ?? null;

        $this->user = new User();
        $this->user->userName = $response['user']['userName'] ?? null;

        if (!empty($response['products'])) {
            foreach ($response['products'] as $productData) {
                $product = new Product();
                $product->gtin = $productData['gtin'] ?? null;
                $product->description = $productData['description'] ?? null;
                $product->index = $productData['index'] ?? null;
                $product->price = $productData['price'] ?? null;
                $product->volume = $productData['volume'] ?? null;
                $product->quantity = $productData['quantity'] ?? null;
                $product->unity = $productData['unity'] ?? null;
                $product->status = $productData['status'] ?? null;
                $this->products[] = $product;
            }
        }

        $this->recalculateProducts = $response['recalculateProducts'] ?? [];
        $this->errorMessage = $response['errorMessage'] ?? [];
    }
}