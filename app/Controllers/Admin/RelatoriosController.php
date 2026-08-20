<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\RelatorioService;

class RelatoriosController extends BaseController
{
    protected RelatorioService $relatorioService;

    public function __construct()
    {
        $this->relatorioService = new RelatorioService();
    }

    /**
     * Dashboard analítico principal de relatórios e métricas.
     */
    public function index()
    {
        $periodo    = $this->request->getGet('periodo') ?? '30d';
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');

        $periodoInfo = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);

        $data = [
            'title'        => 'Relatórios & Métricas Analíticas',
            'periodoInfo'  => $periodoInfo,
            'kpis'         => $this->relatorioService->getKpis($periodoInfo['inicio'], $periodoInfo['fim']),
            'evolucao'     => $this->relatorioService->getEvolucaoVendas($periodoInfo['inicio'], $periodoInfo['fim']),
            'pagamentos'   => $this->relatorioService->getVendasPorFormaPagamento($periodoInfo['inicio'], $periodoInfo['fim']),
            'categorias'   => $this->relatorioService->getFaturamentoPorCategoria($periodoInfo['inicio'], $periodoInfo['fim']),
            'statusDist'   => $this->relatorioService->getStatusDistribuicao($periodoInfo['inicio'], $periodoInfo['fim']),
            'topProdutos'  => $this->relatorioService->getTopProdutos($periodoInfo['inicio'], $periodoInfo['fim'], 5),
            'topClientes'  => $this->relatorioService->getTopClientes($periodoInfo['inicio'], $periodoInfo['fim'], 5),
        ];

        return view('admin/relatorios/index', $data);
    }

    /**
     * Relatório tabular detalhado de vendas.
     */
    public function vendas()
    {
        $pedidoModel = new \App\Models\PedidoModel();

        $periodo        = $this->request->getGet('periodo') ?? '30d';
        $dataInicio     = $this->request->getGet('data_inicio');
        $dataFim        = $this->request->getGet('data_fim');
        $status         = $this->request->getGet('status');
        $formaPagamento = $this->request->getGet('forma_pagamento');
        $cupom          = $this->request->getGet('cupom');

        $periodoInfo = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);

        $filtros = [
            'status'          => $status,
            'forma_pagamento' => $formaPagamento,
            'cupom'           => $cupom,
        ];

        $vendas = $pedidoModel->getVendasRelatorio($periodoInfo['inicio'], $periodoInfo['fim'], $filtros, 20);

        $data = [
            'title'       => 'Relatório de Vendas',
            'periodoInfo' => $periodoInfo,
            'filtros'     => $filtros,
            'kpis'        => $this->relatorioService->getKpis($periodoInfo['inicio'], $periodoInfo['fim']),
            'vendas'      => $vendas,
            'pager'       => $pedidoModel->pager,
        ];

        return view('admin/relatorios/vendas', $data);
    }

    /**
     * Relatório detalhado e ranking de produtos mais vendidos.
     */
    public function produtos()
    {
        $periodo    = $this->request->getGet('periodo') ?? '30d';
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');
        $page       = (int) ($this->request->getGet('page') ?? 1);
        $page       = max(1, $page);
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        $periodoInfo   = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);
        $totalProdutos = $this->relatorioService->getTotalTopProdutos($periodoInfo['inicio'], $periodoInfo['fim']);
        $produtos      = $this->relatorioService->getTopProdutos($periodoInfo['inicio'], $periodoInfo['fim'], $perPage, $offset);

        $pager = service('pager');
        $pagerLinks = $pager->makeLinks($page, $perPage, $totalProdutos, 'bootstrap_pagination');

        $data = [
            'title'         => 'Relatório de Produtos Mais Vendidos',
            'periodoInfo'   => $periodoInfo,
            'produtos'      => $produtos,
            'kpis'          => $this->relatorioService->getKpis($periodoInfo['inicio'], $periodoInfo['fim']),
            'totalProdutos' => $totalProdutos,
            'page'          => $page,
            'perPage'       => $perPage,
            'pagerLinks'    => $pagerLinks,
        ];

        return view('admin/relatorios/produtos', $data);
    }

    /**
     * Relatório de clientes e volume de compras (LTV).
     */
    public function clientes()
    {
        $periodo    = $this->request->getGet('periodo') ?? '30d';
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');
        $page       = (int) ($this->request->getGet('page') ?? 1);
        $page       = max(1, $page);
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        $periodoInfo   = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);
        $totalClientes = $this->relatorioService->getTotalTopClientes($periodoInfo['inicio'], $periodoInfo['fim']);
        $clientes      = $this->relatorioService->getTopClientes($periodoInfo['inicio'], $periodoInfo['fim'], $perPage, $offset);

        $pager = service('pager');
        $pagerLinks = $pager->makeLinks($page, $perPage, $totalClientes, 'bootstrap_pagination');

        $data = [
            'title'         => 'Relatório de Clientes & Compras',
            'periodoInfo'   => $periodoInfo,
            'clientes'      => $clientes,
            'kpis'          => $this->relatorioService->getKpis($periodoInfo['inicio'], $periodoInfo['fim']),
            'totalClientes' => $totalClientes,
            'page'          => $page,
            'perPage'       => $perPage,
            'pagerLinks'    => $pagerLinks,
        ];

        return view('admin/relatorios/clientes', $data);
    }

    /**
     * Relatório de desempenho de cupons de desconto.
     */
    public function cupons()
    {
        $periodo    = $this->request->getGet('periodo') ?? '30d';
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');
        $page       = (int) ($this->request->getGet('page') ?? 1);
        $page       = max(1, $page);
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        $periodoInfo = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);
        $totalCupons = $this->relatorioService->getTotalRelatorioCupons($periodoInfo['inicio'], $periodoInfo['fim']);
        $cupons      = $this->relatorioService->getRelatorioCupons($periodoInfo['inicio'], $periodoInfo['fim'], $perPage, $offset);

        $pager = service('pager');
        $pagerLinks = $pager->makeLinks($page, $perPage, $totalCupons, 'bootstrap_pagination');

        $data = [
            'title'       => 'Relatório de Desempenho de Cupons',
            'periodoInfo' => $periodoInfo,
            'cupons'      => $cupons,
            'kpis'        => $this->relatorioService->getKpis($periodoInfo['inicio'], $periodoInfo['fim']),
            'totalCupons' => $totalCupons,
            'page'        => $page,
            'perPage'     => $perPage,
            'pagerLinks'  => $pagerLinks,
        ];

        return view('admin/relatorios/cupons', $data);
    }

    /**
     * Endpoint para exportação de dados em CSV.
     */
    public function exportar(string $tipo)
    {
        $tiposValidos = ['vendas', 'produtos', 'clientes', 'cupons', 'resumo_kpis'];
        if (!in_array($tipo, $tiposValidos, true)) {
            return redirect()->back()->with('erro', 'Tipo de relatório para exportação inválido.');
        }

        $periodo    = $this->request->getGet('periodo') ?? '30d';
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim    = $this->request->getGet('data_fim');

        $filtros = [
            'status'          => $this->request->getGet('status'),
            'forma_pagamento' => $this->request->getGet('forma_pagamento'),
            'cupom'           => $this->request->getGet('cupom'),
        ];

        $periodoInfo = $this->relatorioService->getPeriodoDatas($periodo, $dataInicio, $dataFim);
        $csvConteudo = $this->relatorioService->gerarCsv($tipo, $periodoInfo['inicio'], $periodoInfo['fim'], $filtros);

        $nomeArquivo = 'relatorio_' . $tipo . '_' . date('Ymd_His') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($csvConteudo);
    }
}
