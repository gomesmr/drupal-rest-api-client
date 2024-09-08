<?php

namespace Drupal\fts\Model;


class Company
{
    public $companyName;
    public $localization;

    public function __construct(array $data) {
        $this->companyName = $data['companyName'] ?? null;
        $this->localization = $data['localization'] ?? null;
    }
}