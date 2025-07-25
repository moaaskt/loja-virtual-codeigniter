<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoProdutoModel extends Model
{

    public function getProdutosDePedidos(array $pedidoIds)
{
    if (empty($pedidoIds)) {
        return [];
    }

    $this->select('pedido_produtos.*, produtos.nome, produtos.imagem');
    $this->join('produtos', 'produtos.id = pedido_produtos.produto_id');
    $this->whereIn('pedido_produtos.pedido_id', $pedidoIds); // whereIn é para arrays!

    return $this->findAll();
}

    protected $table = 'pedido_produtos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'];

    // Desabilitamos os timestamps pois não temos essas colunas nesta tabela
    protected $useTimestamps = false;
}