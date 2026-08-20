<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\RelatorioService;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Models\UsuarioModel;

class RelatorioTest extends CIUnitTestCase
{
    protected RelatorioService $relatorioService;
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected ProdutoModel $produtoModel;
    protected CategoriaModel $categoriaModel;
    protected UsuarioModel $usuarioModel;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        helper(['status', 'form', 'url']);
        $this->db                 = \Config\Database::connect('default');
        $this->relatorioService   = new RelatorioService($this->db);
        $this->pedidoModel        = new PedidoModel($this->db);
        $this->pedidoProdutoModel = new PedidoProdutoModel($this->db);
        $this->produtoModel       = new ProdutoModel($this->db);
        $this->categoriaModel     = new CategoriaModel($this->db);
        $this->usuarioModel       = new UsuarioModel($this->db);
    }

    public function testPeriodoDatasNormalizacao(): void
    {
        // 1. Hoje
        $hoje = $this->relatorioService->getPeriodoDatas('hoje');
        $this->assertEquals('hoje', $hoje['periodo']);
        $this->assertEquals(date('Y-m-d 00:00:00'), $hoje['inicio']);
        $this->assertEquals(date('Y-m-d 23:59:59'), $hoje['fim']);

        // 2. 7 Dias
        $seteDias = $this->relatorioService->getPeriodoDatas('7d');
        $this->assertEquals('7d', $seteDias['periodo']);
        $this->assertEquals(date('Y-m-d 00:00:00', strtotime('-6 days')), $seteDias['inicio']);

        // 3. 30 Dias
        $trintaDias = $this->relatorioService->getPeriodoDatas('30d');
        $this->assertEquals('30d', $trintaDias['periodo']);

        // 4. Mês Atual
        $mesAtual = $this->relatorioService->getPeriodoDatas('mes_atual');
        $this->assertEquals(date('Y-m-01 00:00:00'), $mesAtual['inicio']);
        $this->assertEquals(date('Y-m-t 23:59:59'), $mesAtual['fim']);

        // 5. Ano Atual
        $anoAtual = $this->relatorioService->getPeriodoDatas('ano_atual');
        $this->assertEquals(date('Y-01-01 00:00:00'), $anoAtual['inicio']);
        $this->assertEquals(date('Y-12-31 23:59:59'), $anoAtual['fim']);

        // 6. Custom
        $custom = $this->relatorioService->getPeriodoDatas('custom', '2026-08-01', '2026-08-15');
        $this->assertEquals('custom', $custom['periodo']);
        $this->assertEquals('2026-08-01 00:00:00', $custom['inicio']);
        $this->assertEquals('2026-08-15 23:59:59', $custom['fim']);
    }

    public function testCalculoKpisComPedidos(): void
    {
        // Cria usuário de teste
        $usuarioId = $this->usuarioModel->insert([
            'nome'             => 'Cliente Teste Relatorio',
            'email'            => 'relatorio_kpi_' . uniqid() . '@teste.com',
            'senha_hash'       => '123456',
            'password_confirm' => '123456',
            'role'             => 'cliente',
            'ativo'            => 1,
        ]);

        // Cria categoria e produto
        $catId = $this->categoriaModel->insert([
            'nome'      => 'Categoria Relatorio ' . uniqid(),
            'descricao' => 'Desc'
        ]);

        $prodId = $this->produtoModel->insert([
            'nome'         => 'Produto Relatorio ' . uniqid(),
            'preco'        => 100.00,
            'estoque'      => 50,
            'categoria_id' => $catId,
        ]);

        $dataAgora = date('Y-m-d H:i:s');

        // Cria Pedido 1 (Pago - R$ 200,00)
        $pedido1Id = $this->pedidoModel->insert([
            'usuario_id'       => $usuarioId,
            'valor_total'      => 200.00,
            'cupom_codigo'     => 'DESC10',
            'desconto_valor'   => 20.00,
            'frete_modalidade' => 'SEDEX',
            'frete_valor'      => 15.00,
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pago',
            'status'           => 'pago',
            'criado_em'        => $dataAgora,
        ]);

        $this->pedidoProdutoModel->insert([
            'pedido_id'      => $pedido1Id,
            'produto_id'     => $prodId,
            'quantidade'     => 2,
            'preco_unitario' => 100.00
        ]);

        // Cria Pedido 2 (Pendente - R$ 100,00)
        $pedido2Id = $this->pedidoModel->insert([
            'usuario_id'       => $usuarioId,
            'valor_total'      => 100.00,
            'forma_pagamento'  => 'cartao_credito',
            'status_pagamento' => 'pendente',
            'status'           => 'pendente',
            'criado_em'        => $dataAgora,
        ]);

        $inicio = date('Y-m-d 00:00:00');
        $fim    = date('Y-m-d 23:59:59');

        $kpis = $this->relatorioService->getKpis($inicio, $fim);

        $this->assertGreaterThanOrEqual(200.00, $kpis['faturamento_total']);
        $this->assertGreaterThanOrEqual(2, $kpis['total_pedidos']);
        $this->assertGreaterThanOrEqual(1, $kpis['pedidos_pagos']);
        $this->assertGreaterThan(0, $kpis['ticket_medio']);
        $this->assertGreaterThan(0, $kpis['taxa_conversao']);
        $this->assertGreaterThanOrEqual(1, $kpis['novos_clientes']);
        $this->assertGreaterThanOrEqual(20.00, $kpis['total_descontos']);
        $this->assertGreaterThanOrEqual(15.00, $kpis['total_frete']);
        $this->assertGreaterThanOrEqual(2, $kpis['total_itens_vendidos']);
    }

    public function testEvolucaoVendas(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-6 days'));
        $fim    = date('Y-m-d 23:59:59');

        $evolucao = $this->relatorioService->getEvolucaoVendas($inicio, $fim);

        $this->assertArrayHasKey('labels', $evolucao);
        $this->assertArrayHasKey('faturamento', $evolucao);
        $this->assertArrayHasKey('pedidos', $evolucao);
        $this->assertArrayHasKey('pagos', $evolucao);
        $this->assertCount(7, $evolucao['labels'], 'Evolução de 7 dias deve conter exatamente 7 pontos/labels.');
    }

    public function testVendasPorFormaPagamento(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $pagamentos = $this->relatorioService->getVendasPorFormaPagamento($inicio, $fim);

        $this->assertArrayHasKey('labels', $pagamentos);
        $this->assertArrayHasKey('totais', $pagamentos);
        $this->assertArrayHasKey('qtds', $pagamentos);
        $this->assertNotEmpty($pagamentos['labels']);
    }

    public function testFaturamentoPorCategoria(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $categorias = $this->relatorioService->getFaturamentoPorCategoria($inicio, $fim);

        $this->assertArrayHasKey('labels', $categorias);
        $this->assertArrayHasKey('totais', $categorias);
        $this->assertArrayHasKey('itens', $categorias);
        $this->assertNotEmpty($categorias['labels']);
    }

    public function testStatusDistribuicao(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $status = $this->relatorioService->getStatusDistribuicao($inicio, $fim);

        $this->assertArrayHasKey('labels', $status);
        $this->assertArrayHasKey('data', $status);
        $this->assertNotEmpty($status['labels']);
    }

    public function testTopProdutosERankings(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $topProdutos = $this->relatorioService->getTopProdutos($inicio, $fim, 5);
        $this->assertIsArray($topProdutos);

        if (!empty($topProdutos)) {
            $primeiro = $topProdutos[0];
            $this->assertArrayHasKey('id', $primeiro);
            $this->assertArrayHasKey('nome', $primeiro);
            $this->assertArrayHasKey('total_vendido', $primeiro);
            $this->assertArrayHasKey('receita_total', $primeiro);
        }
    }

    public function testTopClientes(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $topClientes = $this->relatorioService->getTopClientes($inicio, $fim, 5);
        $this->assertIsArray($topClientes);

        if (!empty($topClientes)) {
            $primeiro = $topClientes[0];
            $this->assertArrayHasKey('id', $primeiro);
            $this->assertArrayHasKey('nome', $primeiro);
            $this->assertArrayHasKey('email', $primeiro);
            $this->assertArrayHasKey('total_pedidos', $primeiro);
            $this->assertArrayHasKey('total_gasto', $primeiro);
        }
    }

    public function testRelatorioCupons(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $cupons = $this->relatorioService->getRelatorioCupons($inicio, $fim);
        $this->assertIsArray($cupons);

        if (!empty($cupons)) {
            $primeiro = $cupons[0];
            $this->assertArrayHasKey('codigo', $primeiro);
            $this->assertArrayHasKey('total_usos', $primeiro);
            $this->assertArrayHasKey('total_desconto', $primeiro);
        }
    }

    public function testVendasDetalhadasEFiltros(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $vendasPix = $this->relatorioService->getVendasDetalhadas($inicio, $fim, ['forma_pagamento' => 'pix']);
        $this->assertIsArray($vendasPix);
        foreach ($vendasPix as $v) {
            $this->assertEquals('pix', $v['forma_pagamento']);
        }
    }

    public function testGeracaoCsvComBOM(): void
    {
        $inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $fim    = date('Y-m-d 23:59:59');

        $tipos = ['vendas', 'produtos', 'clientes', 'cupons', 'resumo_kpis'];

        foreach ($tipos as $tipo) {
            $csv = $this->relatorioService->gerarCsv($tipo, $inicio, $fim);
            
            // Verifica se começa com o Byte Order Mark (BOM) UTF-8
            $this->assertStringStartsWith("\xEF\xBB\xBF", $csv, "O CSV do tipo '{$tipo}' deve iniciar com BOM UTF-8.");
            
            // Verifica presença de delimitador ponto-e-vírgula e quebra de linha
            $this->assertStringContainsString(';', $csv, "O CSV do tipo '{$tipo}' deve conter delimitadores ';'.");
            $this->assertStringContainsString("\n", $csv, "O CSV do tipo '{$tipo}' deve conter linhas.");
        }
    }
}
