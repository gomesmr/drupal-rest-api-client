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
    public $marca; // Novo campo para a marca
    public $apresentacao; // Novo campo para o sub-objeto apresentacao
    public $validacao; // Novo campo para validacao

    public function __construct(array $data)
    {
        // Atribuições do produto anterior
        $this->gtin = $data['gtin'] ?? null;
        $this->description = $data['description'] ?? $data['descricao'] ?? null; // Adaptando "description" ou "descricao"
        $this->index = $data['index'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->volume = $data['volume'] ?? $data['apresentacao']['volume'] ?? null; // Pega o volume dentro de apresentacao
        $this->quantity = $data['quantity'] ?? $data['apresentacao']['itemQuantidade'] ?? null;
        $this->unity = $data['unity'] ?? $data['apresentacao']['unidade'] ?? null;
        $this->status = $data['status'] ?? null;

        // Novos campos do novo formato
        $this->marca = $data['marca'] ?? null;
        $this->validacao = $data['validacao'] ?? null;

        // Mapeando o objeto apresentacao, se existir
        $this->apresentacao = isset($data['apresentacao']) ? new Apresentacao($data['apresentacao']) : null;
    }
}

class Apresentacao
{
    public $embalagemTipoEntity;
    public $embalagemQuantidade;
    public $itemQuantidade;
    public $volume;
    public $unidade;

    public function __construct(array $data)
    {
        $this->embalagemTipoEntity = $data['embalagemTipoEntity'] ?? null;
        $this->embalagemQuantidade = $data['embalagemQuantidade'] ?? null;
        $this->itemQuantidade = $data['itemQuantidade'] ?? null;
        $this->volume = $data['volume'] ?? null;
        $this->unidade = $data['unidade'] ?? null;
    }
}
