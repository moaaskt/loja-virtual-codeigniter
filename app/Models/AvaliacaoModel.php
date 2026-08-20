<?php

namespace App\Models;

use CodeIgniter\Model;

class AvaliacaoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'avaliacoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'produto_id',
        'usuario_id',
        'pedido_id',
        'nota',
        'titulo',
        'comentario',
        'status',
        'compra_verificada',
        'created_at',
        'updated_at',
    ];

    // Datas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Regras de validação
    protected $validationRules = [
        'produto_id' => 'required|is_natural_no_zero',
        'usuario_id' => 'required|is_natural_no_zero',
        'nota'       => 'required|in_list[1,2,3,4,5]',
        'comentario' => 'required|min_length[5]|max_length[2000]',
        'titulo'     => 'permit_empty|max_length[150]',
        'status'     => 'permit_empty|in_list[pendente,aprovada,rejeitada]',
    ];

    protected $validationMessages = [
        'produto_id' => [
            'required'           => 'O produto é obrigatório.',
            'is_natural_no_zero' => 'Produto inválido.',
        ],
        'usuario_id' => [
            'required'           => 'O usuário é obrigatório.',
            'is_natural_no_zero' => 'Usuário inválido.',
        ],
        'nota' => [
            'required' => 'Por favor, selecione uma nota de 1 a 5 estrelas.',
            'in_list'  => 'A nota deve ser entre 1 e 5 estrelas.',
        ],
        'comentario' => [
            'required'   => 'Por favor, escreva um comentário sobre o produto.',
            'min_length' => 'O comentário deve conter no mínimo 5 caracteres.',
            'max_length' => 'O comentário não pode exceder 2000 caracteres.',
        ],
    ];

    /**
     * Retorna avaliações aprovadas (ou todas) de um produto com dados do autor.
     */
    public function getAvaliacoesPorProduto(int $produtoId, int $limit = 10, bool $apenasAprovadas = true): array
    {
        $builder = $this->select('avaliacoes.*, usuarios.nome as usuario_nome, usuarios.email as usuario_email')
            ->join('usuarios', 'usuarios.id = avaliacoes.usuario_id', 'left')
            ->where('avaliacoes.produto_id', $produtoId);

        if ($apenasAprovadas) {
            $builder->where('avaliacoes.status', 'aprovada');
        }

        return $builder->orderBy('avaliacoes.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Calcula métricas e estatísticas de avaliações de um produto (média, total, distribuição).
     */
    public function getEstatisticasProduto(int $produtoId): array
    {
        $rows = $this->select('nota, COUNT(*) as qtd')
            ->where('produto_id', $produtoId)
            ->where('status', 'aprovada')
            ->groupBy('nota')
            ->findAll();

        $distribuicao = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $total        = 0;
        $somaNotas    = 0;

        foreach ($rows as $row) {
            $nota = (int) $row['nota'];
            $qtd  = (int) $row['qtd'];
            if (isset($distribuicao[$nota])) {
                $distribuicao[$nota] = $qtd;
            }
            $total     += $qtd;
            $somaNotas += ($nota * $qtd);
        }

        $media = $total > 0 ? round($somaNotas / $total, 1) : 0.0;

        $percentuais = [];
        foreach ($distribuicao as $nota => $qtd) {
            $percentuais[$nota] = $total > 0 ? round(($qtd / $total) * 100) : 0;
        }

        $positivas = ($distribuicao[5] + $distribuicao[4]);
        $recomendacao = $total > 0 ? round(($positivas / $total) * 100) : 0;

        return [
            'media'                  => $media,
            'total'                  => $total,
            'distribuicao'           => $distribuicao,
            'percentuais'            => $percentuais,
            'recomendacao_percentual'=> $recomendacao,
        ];
    }

    /**
     * Verifica se o usuário já avaliou o produto e se realizou compra verificada.
     */
    public function usuarioPodeAvaliar(?int $usuarioId, int $produtoId): array
    {
        if (empty($usuarioId)) {
            return [
                'pode_avaliar'        => false,
                'motivo'              => 'deslogado',
                'ja_avaliou'          => false,
                'avaliacao_existente' => null,
                'comprou'             => false,
                'pedido_id'           => null,
            ];
        }

        // Verifica se já enviou avaliação
        $avaliacaoExistente = $this->where('produto_id', $produtoId)
            ->where('usuario_id', $usuarioId)
            ->first();

        // Verifica se comprou o produto em um pedido válido
        $db = $this->db;
        $pedidoRow = $db->table('pedido_produtos')
            ->select('pedido_produtos.pedido_id')
            ->join('pedidos', 'pedidos.id = pedido_produtos.pedido_id')
            ->where('pedido_produtos.produto_id', $produtoId)
            ->where('pedidos.usuario_id', $usuarioId)
            ->groupStart()
                ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue', 'processando'])
                ->orWhereIn('pedidos.status_pagamento', ['pago', 'aprovado'])
            ->groupEnd()
            ->orderBy('pedidos.criado_em', 'DESC')
            ->get()
            ->getRowArray();

        $comprou  = ($pedidoRow !== null);
        $pedidoId = $comprou ? (int) $pedidoRow['pedido_id'] : null;

        return [
            'pode_avaliar'        => true,
            'motivo'              => null,
            'ja_avaliou'          => ($avaliacaoExistente !== null),
            'avaliacao_existente' => $avaliacaoExistente,
            'comprou'             => $comprou,
            'pedido_id'           => $pedidoId,
        ];
    }

    /**
     * Retorna avaliações com filtros e paginação para o Painel Administrativo.
     */
    public function getAvaliacoesComFiltros(array $filtros = [], int $perPage = 15): array
    {
        $this->select('avaliacoes.*, produtos.nome as produto_nome, produtos.imagem as produto_imagem, usuarios.nome as usuario_nome, usuarios.email as usuario_email')
            ->join('produtos', 'produtos.id = avaliacoes.produto_id', 'left')
            ->join('usuarios', 'usuarios.id = avaliacoes.usuario_id', 'left');

        if (!empty($filtros['status']) && in_array($filtros['status'], ['pendente', 'aprovada', 'rejeitada'])) {
            $this->where('avaliacoes.status', $filtros['status']);
        }

        if (!empty($filtros['nota']) && is_numeric($filtros['nota'])) {
            $this->where('avaliacoes.nota', (int) $filtros['nota']);
        }

        if (!empty($filtros['produto_id']) && is_numeric($filtros['produto_id'])) {
            $this->where('avaliacoes.produto_id', (int) $filtros['produto_id']);
        }

        if (!empty($filtros['busca'])) {
            $busca = trim($filtros['busca']);
            $this->groupStart()
                ->like('produtos.nome', $busca)
                ->orLike('usuarios.nome', $busca)
                ->orLike('usuarios.email', $busca)
                ->orLike('avaliacoes.titulo', $busca)
                ->orLike('avaliacoes.comentario', $busca)
            ->groupEnd();
        }

        return $this->orderBy('avaliacoes.created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Retorna contadores para os cards do painel de moderação.
     */
    public function getContadoresStatus(): array
    {
        $db = $this->db;
        
        $total      = $this->builder()->countAllResults();
        $pendentes  = $this->builder()->where('status', 'pendente')->countAllResults();
        $aprovadas  = $this->builder()->where('status', 'aprovada')->countAllResults();
        $rejeitadas = $this->builder()->where('status', 'rejeitada')->countAllResults();

        $mediaRow = $db->table('avaliacoes')
            ->select('AVG(nota) as media_geral')
            ->where('status', 'aprovada')
            ->get()
            ->getRowArray();

        $mediaGeral = !empty($mediaRow['media_geral']) ? round((float) $mediaRow['media_geral'], 1) : 0.0;

        return [
            'total'       => $total,
            'pendentes'   => $pendentes,
            'aprovadas'   => $aprovadas,
            'rejeitadas'  => $rejeitadas,
            'media_geral' => $mediaGeral,
        ];
    }
}
