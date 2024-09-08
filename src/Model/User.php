<?php

namespace Drupal\fts\Model;


class User
{
    public $userName;

    public function __construct(array $data) {
        $this->userName = $data['userName'] ?? null;
    }
}