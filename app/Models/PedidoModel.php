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
        'codigo_rastreio',
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

    public function atualizarRastreio(int $pedidoId, string $codigoRastreio): bool
    {
        return $this->update($pedidoId, ['codigo_rastreio' => $codigoRastreio]);
    }

    public function atualizarStatusPagamento(int $pedidoId, string $statusPagamento, ?string $novoStatusPedido = null): bool
    {
        $dados = ['status_pagamento' => $statusPagamento];
        if ($novoStatusPedido !== null) {
            $dados['status'] = $novoStatusPedido;
        }

        return $this->update($pedidoId, $dados);
    }

    /**
     * Retorna a listagem de vendas com filtros e paginação nativa do CodeIgniter.
     */
    public function getVendasRelatorio(string $dataInicio, string $dataFim, array $filtros = [], int $perPage = 20)
    {
        $this->select('pedidos.*, usuarios.nome as cliente_nome, usuarios.email as cliente_email');
        $this->join('usuarios', 'usuarios.id = pedidos.usuario_id', 'left');
        $this->where('pedidos.criado_em >=', $dataInicio);
        $this->where('pedidos.criado_em <=', $dataFim);

        if (!empty($filtros['status'])) {
            $this->where('pedidos.status', $filtros['status']);
        }
        if (!empty($filtros['forma_pagamento'])) {
            $this->where('pedidos.forma_pagamento', $filtros['forma_pagamento']);
        }
        if (!empty($filtros['cupom'])) {
            $this->where('pedidos.cupom_codigo', $filtros['cupom']);
        }

        $this->orderBy('pedidos.criado_em', 'DESC');

        return $this->paginate($perPage);
    }
}