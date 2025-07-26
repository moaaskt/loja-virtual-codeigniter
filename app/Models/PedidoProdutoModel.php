<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoProdutoModel extends Model
{
    protected $table            = 'pedido_produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'];

    protected $useTimestamps = false; 

    /**
     * Busca os produtos de um ÚNICO pedido.
     * Usado na página de detalhes do pedido do admin.
     */
    public function getProdutosDePedido($pedidoId)
    {
        if (empty($pedidoId)) {
            return [];
        }
        $this->select('pedido_produtos.*, produtos.nome, produtos.imagem');
        $this->join('produtos', 'produtos.id = pedido_produtos.produto_id');
        $this->where('pedido_produtos.pedido_id', $pedidoId);

        return $this->findAll();
    }

    /**
     * Busca todos os produtos de uma LISTA de IDs de pedidos.
     * Usado na página "Meus Pedidos" do cliente.
     */
    public function getProdutosDePedidos(array $pedidoIds)
    {
        if (empty($pedidoIds)) {
            return [];
        }

        $this->select('pedido_produtos.*, produtos.nome, produtos.imagem');
        $this->join('produtos', 'produtos.id = pedido_produtos.produto_id');
        $this->whereIn('pedido_produtos.pedido_id', $pedidoIds);

        return $this->findAll();
    }
}