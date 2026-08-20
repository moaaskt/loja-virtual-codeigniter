<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class RelatorioService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * Normaliza e valida o intervalo de datas com base no período selecionado.
     */
    public function getPeriodoDatas(?string $periodo = '30d', ?string $dataInicio = null, ?string $dataFim = null): array
    {
        $periodo = $periodo ?: '30d';

        switch ($periodo) {
            case 'hoje':
                $inicio = date('Y-m-d 00:00:00');
                $fim    = date('Y-m-d 23:59:59');
                $label  = 'Hoje (' . date('d/m/Y') . ')';
                break;

            case '7d':
                $inicio = date('Y-m-d 00:00:00', strtotime('-6 days'));
                $fim    = date('Y-m-d 23:59:59');
                $label  = 'Últimos 7 Dias';
                break;

            case 'mes_atual':
                $inicio = date('Y-m-01 00:00:00');
                $fim    = date('Y-m-t 23:59:59');
                $label  = 'Mês Atual (' . date('m/Y') . ')';
                break;

            case 'ano_atual':
                $inicio = date('Y-01-01 00:00:00');
                $fim    = date('Y-12-31 23:59:59');
                $label  = 'Ano Atual (' . date('Y') . ')';
                break;

            case 'custom':
                if (!empty($dataInicio) && !empty($dataFim)) {
                    $inicio = date('Y-m-d 00:00:00', strtotime($dataInicio));
                    $fim    = date('Y-m-d 23:59:59', strtotime($dataFim));
                    $label  = date('d/m/Y', strtotime($dataInicio)) . ' até ' . date('d/m/Y', strtotime($dataFim));
                } else {
                    $inicio = date('Y-m-d 00:00:00', strtotime('-29 days'));
                    $fim    = date('Y-m-d 23:59:59');
                    $label  = 'Últimos 30 Dias';
                    $periodo = '30d';
                }
                break;

            case '30d':
            default:
                $inicio  = date('Y-m-d 00:00:00', strtotime('-29 days'));
                $fim     = date('Y-m-d 23:59:59');
                $label   = 'Últimos 30 Dias';
                $periodo = '30d';
                break;
        }

        return [
            'periodo'               => $periodo,
            'inicio'                => $inicio,
            'fim'                   => $fim,
            'data_inicio_formatada' => date('Y-m-d', strtotime($inicio)),
            'data_fim_formatada'    => date('Y-m-d', strtotime($fim)),
            'label'                 => $label,
        ];
    }

    /**
     * Retorna os principais Indicadores-Chave de Desempenho (KPIs) no período.
     */
    public function getKpis(string $dataInicio, string $dataFim): array
    {
        // 1. Faturamento e Pedidos Pagos
        $rowPagos = $this->db->table('pedidos')
            ->select('
                COUNT(id) as pedidos_pagos,
                COALESCE(SUM(valor_total), 0) as faturamento_total,
                COALESCE(SUM(desconto_valor), 0) as total_descontos,
                COALESCE(SUM(frete_valor), 0) as total_frete
            ')
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->whereIn('status', ['pago', 'enviado', 'entregue'])
            ->get()
            ->getRowArray();

        $faturamentoTotal = (float) ($rowPagos['faturamento_total'] ?? 0);
        $pedidosPagos     = (int) ($rowPagos['pedidos_pagos'] ?? 0);
        $totalDescontos   = (float) ($rowPagos['total_descontos'] ?? 0);
        $totalFrete       = (float) ($rowPagos['total_frete'] ?? 0);

        // 2. Total de Pedidos (todos os status)
        $totalPedidos = $this->db->table('pedidos')
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->countAllResults();

        // 3. Ticket Médio
        $ticketMedio = $pedidosPagos > 0 ? ($faturamentoTotal / $pedidosPagos) : 0.0;

        // 4. Taxa de Conversão de Pedidos
        $taxaConversao = $totalPedidos > 0 ? (($pedidosPagos / $totalPedidos) * 100) : 0.0;

        // 5. Novos Clientes Cadastrados
        $novosClientes = $this->db->table('usuarios')
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->where('role', 'cliente')
            ->countAllResults();

        // 6. Total de Itens Vendidos (em pedidos pagos)
        $rowItens = $this->db->table('pedido_produtos')
            ->select('COALESCE(SUM(pedido_produtos.quantidade), 0) as total_itens')
            ->join('pedidos', 'pedidos.id = pedido_produtos.pedido_id')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->get()
            ->getRowArray();

        $totalItensVendidos = (int) ($rowItens['total_itens'] ?? 0);

        return [
            'faturamento_total'    => $faturamentoTotal,
            'total_pedidos'        => $totalPedidos,
            'pedidos_pagos'        => $pedidosPagos,
            'ticket_medio'         => round($ticketMedio, 2),
            'taxa_conversao'       => round($taxaConversao, 1),
            'novos_clientes'       => $novosClientes,
            'total_descontos'      => $totalDescontos,
            'total_frete'          => $totalFrete,
            'total_itens_vendidos' => $totalItensVendidos,
        ];
    }

    /**
     * Retorna a evolução temporal diária de vendas e faturamento para gráficos.
     */
    public function getEvolucaoVendas(string $dataInicio, string $dataFim): array
    {
        $query = $this->db->table('pedidos')
            ->select("
                DATE(criado_em) as data,
                COUNT(id) as total_pedidos,
                COALESCE(SUM(CASE WHEN status IN ('pago', 'enviado', 'entregue') THEN valor_total ELSE 0 END), 0) as faturamento,
                COALESCE(SUM(CASE WHEN status IN ('pago', 'enviado', 'entregue') THEN 1 ELSE 0 END), 0) as pedidos_pagos
            ")
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->groupBy('DATE(criado_em)')
            ->orderBy('data', 'ASC')
            ->get()
            ->getResultArray();

        $mapaDados = [];
        foreach ($query as $row) {
            $mapaDados[$row['data']] = [
                'faturamento'   => (float) $row['faturamento'],
                'total_pedidos' => (int) $row['total_pedidos'],
                'pedidos_pagos' => (int) $row['pedidos_pagos'],
            ];
        }

        // Preenche todas as datas do intervalo sequencialmente
        $inicioTs = strtotime($dataInicio);
        $fimTs    = strtotime($dataFim);

        $labels      = [];
        $faturamento = [];
        $pedidos     = [];
        $pagos       = [];

        for ($ts = $inicioTs; $ts <= $fimTs; $ts = strtotime('+1 day', $ts)) {
            $dataIso = date('Y-m-d', $ts);
            $labels[]      = date('d/m', $ts);
            $faturamento[] = isset($mapaDados[$dataIso]) ? $mapaDados[$dataIso]['faturamento'] : 0.0;
            $pedidos[]     = isset($mapaDados[$dataIso]) ? $mapaDados[$dataIso]['total_pedidos'] : 0;
            $pagos[]       = isset($mapaDados[$dataIso]) ? $mapaDados[$dataIso]['pedidos_pagos'] : 0;
        }

        return [
            'labels'      => $labels,
            'faturamento' => $faturamento,
            'pedidos'     => $pedidos,
            'pagos'       => $pagos,
        ];
    }

    /**
     * Retorna a distribuição de vendas por forma de pagamento.
     */
    public function getVendasPorFormaPagamento(string $dataInicio, string $dataFim): array
    {
        $rows = $this->db->table('pedidos')
            ->select("
                COALESCE(forma_pagamento, 'Nao informado') as forma,
                COUNT(id) as total_pedidos,
                COALESCE(SUM(CASE WHEN status IN ('pago', 'enviado', 'entregue') THEN valor_total ELSE 0 END), 0) as faturamento
            ")
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->groupBy('forma_pagamento')
            ->orderBy('faturamento', 'DESC')
            ->get()
            ->getResultArray();

        $labels = [];
        $totais = [];
        $qtds   = [];

        $nomesFormatados = [
            'pix'            => 'Pix',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao'         => 'Cartão de Crédito',
            'boleto'         => 'Boleto Bancário',
        ];

        foreach ($rows as $row) {
            $formaKey = strtolower($row['forma']);
            $labels[] = $nomesFormatados[$formaKey] ?? ucfirst($row['forma']);
            $totais[] = (float) $row['faturamento'];
            $qtds[]   = (int) $row['total_pedidos'];
        }

        if (empty($labels)) {
            $labels = ['Sem dados'];
            $totais = [0];
            $qtds   = [0];
        }

        return [
            'labels' => $labels,
            'totais' => $totais,
            'qtds'   => $qtds,
        ];
    }

    /**
     * Retorna o faturamento e unidades vendidas agrupados por categoria.
     */
    public function getFaturamentoPorCategoria(string $dataInicio, string $dataFim): array
    {
        $rows = $this->db->table('pedido_produtos')
            ->select("
                COALESCE(categorias.nome, 'Sem Categoria') as categoria,
                COALESCE(SUM(pedido_produtos.quantidade * pedido_produtos.preco_unitario), 0) as faturamento,
                COALESCE(SUM(pedido_produtos.quantidade), 0) as total_itens
            ")
            ->join('pedidos', 'pedidos.id = pedido_produtos.pedido_id')
            ->join('produtos', 'produtos.id = pedido_produtos.produto_id', 'left')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->groupBy('categorias.nome')
            ->orderBy('faturamento', 'DESC')
            ->get()
            ->getResultArray();

        $labels = [];
        $totais = [];
        $itens  = [];

        foreach ($rows as $row) {
            $labels[] = $row['categoria'];
            $totais[] = (float) $row['faturamento'];
            $itens[]  = (int) $row['total_itens'];
        }

        if (empty($labels)) {
            $labels = ['Sem dados'];
            $totais = [0];
            $itens  = [0];
        }

        return [
            'labels' => $labels,
            'totais' => $totais,
            'itens'  => $itens,
        ];
    }

    /**
     * Retorna a contagem e percentual de pedidos por status.
     */
    public function getStatusDistribuicao(string $dataInicio, string $dataFim): array
    {
        $rows = $this->db->table('pedidos')
            ->select('status, COUNT(id) as total')
            ->where('criado_em >=', $dataInicio)
            ->where('criado_em <=', $dataFim)
            ->groupBy('status')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        $labels = [];
        $data   = [];

        $labelsStatus = [
            'pendente'  => 'Pendente',
            'pago'      => 'Pago',
            'enviado'   => 'Enviado',
            'entregue'  => 'Entregue',
            'cancelado' => 'Cancelado',
        ];

        foreach ($rows as $row) {
            $statusKey = strtolower($row['status']);
            $labels[]  = $labelsStatus[$statusKey] ?? ucfirst($row['status']);
            $data[]    = (int) $row['total'];
        }

        if (empty($labels)) {
            $labels = ['Sem dados'];
            $data   = [0];
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * Retorna o ranking dos produtos mais vendidos no período.
     */
    public function getTopProdutos(string $dataInicio, string $dataFim, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->db->table('pedido_produtos')
            ->select("
                produtos.id,
                produtos.nome,
                produtos.imagem,
                produtos.estoque,
                COALESCE(categorias.nome, 'Sem Categoria') as categoria_nome,
                COALESCE(SUM(pedido_produtos.quantidade), 0) as total_vendido,
                COALESCE(SUM(pedido_produtos.quantidade * pedido_produtos.preco_unitario), 0) as receita_total
            ")
            ->join('pedidos', 'pedidos.id = pedido_produtos.pedido_id')
            ->join('produtos', 'produtos.id = pedido_produtos.produto_id')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->groupBy('produtos.id')
            ->orderBy('total_vendido', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Retorna o total de produtos distintos vendidos no período (para paginação).
     */
    public function getTotalTopProdutos(string $dataInicio, string $dataFim): int
    {
        $subquery = $this->db->table('pedido_produtos')
            ->select('produtos.id')
            ->join('pedidos', 'pedidos.id = pedido_produtos.pedido_id')
            ->join('produtos', 'produtos.id = pedido_produtos.produto_id')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->groupBy('produtos.id')
            ->getCompiledSelect();

        return (int) $this->db->table("({$subquery}) as t")->countAllResults();
    }

    /**
     * Retorna o ranking de clientes com maior faturamento/compras no período.
     */
    public function getTopClientes(string $dataInicio, string $dataFim, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->db->table('pedidos')
            ->select("
                usuarios.id,
                usuarios.nome,
                usuarios.email,
                COUNT(pedidos.id) as total_pedidos,
                COALESCE(SUM(pedidos.valor_total), 0) as total_gasto,
                COALESCE(AVG(pedidos.valor_total), 0) as ticket_medio,
                MAX(pedidos.criado_em) as ultimo_pedido
            ")
            ->join('usuarios', 'usuarios.id = pedidos.usuario_id')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->groupBy('usuarios.id')
            ->orderBy('total_gasto', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Retorna o total de clientes com compras pagas no período (para paginação).
     */
    public function getTotalTopClientes(string $dataInicio, string $dataFim): int
    {
        $subquery = $this->db->table('pedidos')
            ->select('usuarios.id')
            ->join('usuarios', 'usuarios.id = pedidos.usuario_id')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->whereIn('pedidos.status', ['pago', 'enviado', 'entregue'])
            ->groupBy('usuarios.id')
            ->getCompiledSelect();

        return (int) $this->db->table("({$subquery}) as t")->countAllResults();
    }

    /**
     * Retorna o relatório de desempenho de cupons de desconto no período.
     */
    public function getRelatorioCupons(string $dataInicio, string $dataFim, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->db->table('pedidos')
            ->select("
                pedidos.cupom_codigo as codigo,
                COUNT(pedidos.id) as total_usos,
                COALESCE(SUM(pedidos.desconto_valor), 0) as total_desconto,
                COALESCE(SUM(pedidos.valor_total), 0) as total_faturamento
            ")
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->where('pedidos.cupom_codigo IS NOT NULL')
            ->where("pedidos.cupom_codigo != ''")
            ->groupBy('pedidos.cupom_codigo')
            ->orderBy('total_desconto', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Retorna o total de cupons distintos utilizados no período (para paginação).
     */
    public function getTotalRelatorioCupons(string $dataInicio, string $dataFim): int
    {
        $subquery = $this->db->table('pedidos')
            ->select('pedidos.cupom_codigo')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim)
            ->where('pedidos.cupom_codigo IS NOT NULL')
            ->where("pedidos.cupom_codigo != ''")
            ->groupBy('pedidos.cupom_codigo')
            ->getCompiledSelect();

        return (int) $this->db->table("({$subquery}) as t")->countAllResults();
    }

    /**
     * Retorna a listagem detalhada de vendas no período com filtros opcionais.
     */
    public function getVendasDetalhadas(string $dataInicio, string $dataFim, array $filtros = []): array
    {
        $builder = $this->db->table('pedidos')
            ->select("
                pedidos.*,
                usuarios.nome as cliente_nome,
                usuarios.email as cliente_email
            ")
            ->join('usuarios', 'usuarios.id = pedidos.usuario_id', 'left')
            ->where('pedidos.criado_em >=', $dataInicio)
            ->where('pedidos.criado_em <=', $dataFim);

        if (!empty($filtros['status'])) {
            $builder->where('pedidos.status', $filtros['status']);
        }

        if (!empty($filtros['forma_pagamento'])) {
            $builder->where('pedidos.forma_pagamento', $filtros['forma_pagamento']);
        }

        if (!empty($filtros['cupom'])) {
            $builder->where('pedidos.cupom_codigo', $filtros['cupom']);
        }

        return $builder->orderBy('pedidos.criado_em', 'DESC')->get()->getResultArray();
    }

    /**
     * Gera e retorna uma string CSV com BOM UTF-8 e cabeçalhos legíveis.
     */
    public function gerarCsv(string $tipo, string $dataInicio, string $dataFim, array $filtros = []): string
    {
        $handle = fopen('php://temp', 'r+');

        // Adiciona UTF-8 BOM para garantir compatibilidade com Microsoft Excel e acentuação correta
        fwrite($handle, "\xEF\xBB\xBF");

        $delimitador = ';';

        switch ($tipo) {
            case 'vendas':
                fputcsv($handle, [
                    'ID Pedido',
                    'Data/Hora',
                    'Cliente',
                    'E-mail',
                    'Status',
                    'Forma de Pagamento',
                    'Cupom',
                    'Desconto (R$)',
                    'Frete (R$)',
                    'Valor Total (R$)'
                ], $delimitador);

                $vendas = $this->getVendasDetalhadas($dataInicio, $dataFim, $filtros);
                foreach ($vendas as $v) {
                    fputcsv($handle, [
                        '#' . $v['id'],
                        date('d/m/Y H:i', strtotime($v['criado_em'])),
                        $v['cliente_nome'] ?? 'Cliente não identificado',
                        $v['cliente_email'] ?? '',
                        ucfirst($v['status']),
                        $v['forma_pagamento'] ? ucfirst(str_replace('_', ' ', $v['forma_pagamento'])) : 'N/A',
                        $v['cupom_codigo'] ?: 'Nenhum',
                        number_format((float) ($v['desconto_valor'] ?? 0), 2, ',', '.'),
                        number_format((float) ($v['frete_valor'] ?? 0), 2, ',', '.'),
                        number_format((float) ($v['valor_total'] ?? 0), 2, ',', '.')
                    ], $delimitador);
                }
                break;

            case 'produtos':
                fputcsv($handle, [
                    'ID',
                    'Produto',
                    'Categoria',
                    'Estoque Atual',
                    'Unidades Vendidas',
                    'Receita Gerada (R$)'
                ], $delimitador);

                $produtos = $this->getTopProdutos($dataInicio, $dataFim, 0);
                foreach ($produtos as $p) {
                    fputcsv($handle, [
                        $p['id'],
                        $p['nome'],
                        $p['categoria_nome'] ?? 'Sem Categoria',
                        $p['estoque'],
                        $p['total_vendido'],
                        number_format((float) $p['receita_total'], 2, ',', '.')
                    ], $delimitador);
                }
                break;

            case 'clientes':
                fputcsv($handle, [
                    'ID',
                    'Cliente',
                    'E-mail',
                    'Total de Pedidos',
                    'Total Gasto (R$)',
                    'Ticket Médio (R$)',
                    'Último Pedido'
                ], $delimitador);

                $clientes = $this->getTopClientes($dataInicio, $dataFim, 0);
                foreach ($clientes as $c) {
                    fputcsv($handle, [
                        $c['id'],
                        $c['nome'],
                        $c['email'],
                        $c['total_pedidos'],
                        number_format((float) $c['total_gasto'], 2, ',', '.'),
                        number_format((float) $c['ticket_medio'], 2, ',', '.'),
                        $c['ultimo_pedido'] ? date('d/m/Y H:i', strtotime($c['ultimo_pedido'])) : 'N/A'
                    ], $delimitador);
                }
                break;

            case 'cupons':
                fputcsv($handle, [
                    'Código do Cupom',
                    'Total de Utilizações',
                    'Total em Descontos (R$)',
                    'Faturamento com o Cupom (R$)'
                ], $delimitador);

                $cupons = $this->getRelatorioCupons($dataInicio, $dataFim, 0);
                foreach ($cupons as $cp) {
                    fputcsv($handle, [
                        $cp['codigo'],
                        $cp['total_usos'],
                        number_format((float) $cp['total_desconto'], 2, ',', '.'),
                        number_format((float) $cp['total_faturamento'], 2, ',', '.')
                    ], $delimitador);
                }
                break;

            case 'resumo_kpis':
            default:
                fputcsv($handle, ['Métrica', 'Valor'], $delimitador);
                $kpis = $this->getKpis($dataInicio, $dataFim);
                fputcsv($handle, ['Faturamento Total', 'R$ ' . number_format($kpis['faturamento_total'], 2, ',', '.')], $delimitador);
                fputcsv($handle, ['Total de Pedidos', $kpis['total_pedidos']], $delimitador);
                fputcsv($handle, ['Pedidos Pagos/Concluídos', $kpis['pedidos_pagos']], $delimitador);
                fputcsv($handle, ['Taxa de Conversão', number_format($kpis['taxa_conversao'], 1, ',', '.') . '%'], $delimitador);
                fputcsv($handle, ['Ticket Médio', 'R$ ' . number_format($kpis['ticket_medio'], 2, ',', '.')], $delimitador);
                fputcsv($handle, ['Novos Clientes', $kpis['novos_clientes']], $delimitador);
                fputcsv($handle, ['Total em Descontos de Cupons', 'R$ ' . number_format($kpis['total_descontos'], 2, ',', '.')], $delimitador);
                fputcsv($handle, ['Total em Fretes', 'R$ ' . number_format($kpis['total_frete'], 2, ',', '.')], $delimitador);
                fputcsv($handle, ['Total de Itens Vendidos', $kpis['total_itens_vendidos']], $delimitador);
                break;
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
