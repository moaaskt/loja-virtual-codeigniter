<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{

    public function getPedidosPorUsuario($usuarioId)
    {
        // Busca os pedidos ordenando pelos mais recentes primeiro
        return $this->where('usuario_id', $usuarioId)
            ->orderBy('criado_em', 'DESC')
            ->findAll();
    }



    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['usuario_id', 'valor_total', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField = 'criado_em';
    protected $updatedField = ''; // Não temos uma coluna 'atualizado_em'
}