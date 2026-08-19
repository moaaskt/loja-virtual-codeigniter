<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'usuario_id',
        'valor_total',
        'cupom_codigo',
        'desconto_valor',
        'frete_modalidade',
        'frete_valor',
        'forma_pagamento',
        'status_pagamento',
        'status',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = ''; // Não temos coluna atualizado_em em pedidos

    public function getPedidosPorUsuario($usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
            ->orderBy('criado_em', 'DESC')
            ->findAll();
    }

    public function getPedidoComCliente($id)
    {
        $this->select('pedidos.*, usuarios.nome as cliente_nome, usuarios.email as cliente_email');
        $this->join('usuarios', 'usuarios.id = pedidos.usuario_id');
        return $this->find($id);
    }

    public function getAllPedidosComCliente($perPage = 10)
    {
        $this->select('pedidos.*, usuarios.nome as cliente_nome');
        $this->join('usuarios', 'usuarios.id = pedidos.usuario_id');
        $this->orderBy('pedidos.criado_em', 'DESC');

        return $this->paginate($perPage);
    }

    public function atualizarStatusPagamento(int $pedidoId, string $statusPagamento, ?string $novoStatusPedido = null): bool
    {
        $dados = ['status_pagamento' => $statusPagamento];
        if ($novoStatusPedido !== null) {
            $dados['status'] = $novoStatusPedido;
        }

        return $this->update($pedidoId, $dados);
    }
}