<?php

namespace Drupal\fts\Model;

class ProductGetId
{
    public $gtin;
    public $descricao;
    public $marca;
    public $apresentacao; // Objeto 'apresentacao'
    public $validacao;

    public function __construct(array $data)
    {
        // Mapeamento direto dos campos do payload
        $this->gtin = $data['gtin'] ?? null;
        $this->descricao = $data['descricao'] ?? null;
        $this->marca = $data['marca'] ?? null;
        $this->validacao = $data['validacao'] ?? null;

        // Mapeamento do objeto 'apresentacao'
        $this->apresentacao = isset($data['apresentacao']) ? new Apresentacao($data['apresentacao']) : null;
    }
}

class Apresentacao
{
    public $embalagemTipoEntity;
    public $embalagemQuantidade;
    public $itemQuantidade;
    public $descricao;
    public $volume;
    public $unidade;

    public function __construct(array $data)
    {
        // Mapeamento direto dos campos do objeto 'apresentacao'
        $this->embalagemTipoEntity = $data['embalagemTipoEntity'] ?? null;
        $this->embalagemQuantidade = $data['embalagemQuantidade'] ?? null;
        $this->itemQuantidade = $data['itemQuantidade'] ?? null;
        $this->descricao = $data['descricao'] ?? null; // Pode ser nulo
        $this->volume = $data['volume'] ?? null;
        $this->unidade = $data['unidade'] ?? null;
    }
}
