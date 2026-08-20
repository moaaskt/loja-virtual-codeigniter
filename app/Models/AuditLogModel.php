<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'usuario_id',
        'acao',
        'entidade',
        'registro_id',
        'dados_anteriores',
        'dados_novos',
        'ip',
        'user_agent',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = false;

    /**
     * Busca logs de auditoria com filtros e dados do usuário relacionado.
     */
    public function getLogsComFiltros(array $filtros = [], int $perPage = 20): array
    {
        $builder = $this->select('audit_logs.*, usuarios.nome as usuario_nome, usuarios.email as usuario_email')
            ->join('usuarios', 'usuarios.id = audit_logs.usuario_id', 'left');

        if (!empty($filtros['usuario_id'])) {
            $builder->where('audit_logs.usuario_id', (int) $filtros['usuario_id']);
        }

        if (!empty($filtros['acao'])) {
            $builder->where('audit_logs.acao', $filtros['acao']);
        }

        if (!empty($filtros['entidade'])) {
            $builder->where('audit_logs.entidade', $filtros['entidade']);
        }

        if (!empty($filtros['busca'])) {
            $termo = $filtros['busca'];
            $builder->groupStart()
                ->like('audit_logs.dados_anteriores', $termo)
                ->orLike('audit_logs.dados_novos', $termo)
                ->orLike('audit_logs.ip', $termo)
                ->orLike('usuarios.nome', $termo)
                ->orLike('usuarios.email', $termo)
            ->groupEnd();
        }

        return $builder->orderBy('audit_logs.created_at', 'DESC')
            ->paginate($perPage);
    }
}
