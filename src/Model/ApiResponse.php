<?php

namespace Drupal\fts\Model;


class ApiResponse {
    public $products = [];
    public $company;
    public $user;
    public $dateTime;

    public function __construct(array $products, Company $company, User $user, $dateTime) {
        $this->products = $products;
        $this->company = $company;
        $this->user = $user;
        $this->dateTime = $dateTime;
    }
}