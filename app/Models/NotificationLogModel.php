<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationLogModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'notification_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'canal',
        'destinatario',
        'evento',
        'payload',
        'status',
        'tentativas',
        'mensagem_erro',
        'enviado_em',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Busca logs de notificações com filtros.
     */
    public function getLogsComFiltros(array $filtros = [], int $perPage = 20): array
    {
        $builder = $this;

        if (!empty($filtros['canal'])) {
            $builder = $builder->where('canal', $filtros['canal']);
        }

        if (!empty($filtros['status'])) {
            $builder = $builder->where('status', $filtros['status']);
        }

        if (!empty($filtros['evento'])) {
            $builder = $builder->where('evento', $filtros['evento']);
        }

        if (!empty($filtros['busca'])) {
            $termo = $filtros['busca'];
            $builder = $builder->groupStart()
                ->like('destinatario', $termo)
                ->orLike('payload', $termo)
                ->orLike('mensagem_erro', $termo)
            ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Retorna contadores agregados para os cards de estatística.
     */
    public function getEstatisticas(): array
    {
        $total    = $this->countAllResults(false);
        $enviados = $this->where('status', 'enviado')->countAllResults(false);
        $falhas   = $this->where('status', 'falhou')->countAllResults(false);
        $pendentes= $this->where('status', 'pendente')->countAllResults(false);

        return [
            'total'     => $total,
            'enviados'  => $enviados,
            'falhas'    => $falhas,
            'pendentes' => $pendentes,
        ];
    }
}
