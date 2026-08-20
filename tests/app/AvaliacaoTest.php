<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\AvaliacaoModel;
use App\Models\ProdutoModel;
use App\Models\UsuarioModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Services\AuditService;

class AvaliacaoTest extends CIUnitTestCase
{
    protected AvaliacaoModel $avaliacaoModel;
    protected ProdutoModel $produtoModel;
    protected UsuarioModel $usuarioModel;
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        helper(['status', 'form', 'url']);
        $this->db                 = \Config\Database::connect('default');
        $this->avaliacaoModel     = new AvaliacaoModel($this->db);
        $this->produtoModel       = new ProdutoModel($this->db);
        $this->usuarioModel       = new UsuarioModel($this->db);
        $this->pedidoModel        = new PedidoModel($this->db);
        $this->pedidoProdutoModel = new PedidoProdutoModel($this->db);
    }

    /**
     * Cria ou obtém um usuário de teste no banco.
     */
    private function getUsuarioTeste(string $email = 'cliente_avaliacao@teste.com'): int
    {
        $usuario = $this->usuarioModel->where('email', $email)->first();
        if ($usuario) {
            return (int) $usuario['id'];
        }

        return (int) $this->usuarioModel->insert([
            'nome'             => 'Cliente Avaliador Teste',
            'email'            => $email,
            'senha_hash'       => '123456',
            'password_confirm' => '123456',
            'role'             => 'cliente',
            'ativo'            => 1,
        ]);
    }

    /**
     * Cria ou obtém uma categoria de teste no banco.
     */
    private function getCategoriaTeste(): int
    {
        $categoria = $this->db->table('categorias')->get()->getRowArray();
        if ($categoria) {
            return (int) $categoria['id'];
        }

        $this->db->table('categorias')->insert([
            'nome'      => 'Categoria Avaliação Teste',
            'descricao' => 'Descrição Teste',
        ]);
        return (int) $this->db->insertID();
    }

    /**
     * Cria ou obtém um produto de teste no banco.
     */
    private function getProdutoTeste(): int
    {
        $produto = $this->produtoModel->first();
        if ($produto) {
            return (int) $produto['id'];
        }

        $categoriaId = $this->getCategoriaTeste();

        return (int) $this->produtoModel->insert([
            'nome'         => 'Camiseta Review Teste',
            'descricao'    => 'Camiseta de alta qualidade para testes.',
            'preco'        => 89.90,
            'estoque'      => 50,
            'categoria_id' => $categoriaId,
        ]);
    }

    public function testCriarAvaliacaoComSucesso(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        // Limpa avaliações prévias deste usuário/produto para teste isolado
        $this->avaliacaoModel->where('produto_id', $produtoId)->where('usuario_id', $usuarioId)->delete();

        $dados = [
            'produto_id'        => $produtoId,
            'usuario_id'        => $usuarioId,
            'nota'              => 5,
            'titulo'            => 'Produto Excelente!',
            'comentario'        => 'Adorei a qualidade e o tecido, super confortável e veste muito bem.',
            'status'            => 'pendente',
            'compra_verificada' => 1,
        ];

        $avaliacaoId = $this->avaliacaoModel->insert($dados);
        $this->assertIsNumeric($avaliacaoId, 'Avaliação deve ser inserida retornando um ID numérico.');

        $salva = $this->avaliacaoModel->find($avaliacaoId);
        $this->assertNotNull($salva);
        $this->assertEquals(5, (int) $salva['nota']);
        $this->assertEquals('Produto Excelente!', $salva['titulo']);
        $this->assertEquals('pendente', $salva['status']);
        $this->assertEquals(1, (int) $salva['compra_verificada']);
    }

    public function testValidacaoNotaInvalida(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        // Nota 0 (inválida)
        $resultadoZero = $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 0,
            'comentario' => 'Comentário válido com mais de 5 caracteres',
        ]);
        $this->assertFalse($resultadoZero, 'Nota 0 deve ser rejeitada pela validação.');

        // Nota 6 (inválida)
        $resultadoSeis = $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 6,
            'comentario' => 'Comentário válido com mais de 5 caracteres',
        ]);
        $this->assertFalse($resultadoSeis, 'Nota 6 deve ser rejeitada pela validação.');
    }

    public function testValidacaoComentarioObrigatorioECurto(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        // Comentário vazio
        $resVazio = $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 4,
            'comentario' => '',
        ]);
        $this->assertFalse($resVazio, 'Comentário vazio deve ser rejeitado.');

        // Comentário muito curto (< 5 caracteres)
        $resCurto = $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 4,
            'comentario' => 'Oi',
        ]);
        $this->assertFalse($resCurto, 'Comentário com menos de 5 caracteres deve ser rejeitado.');
    }

    public function testCalculoEstatisticasProduto(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        // Limpa avaliações anteriores deste produto
        $this->avaliacaoModel->where('produto_id', $produtoId)->delete();

        // Insere 3 avaliações aprovadas: notas 5, 5, 4 (média esperada: 14/3 = 4.7)
        $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 5,
            'comentario' => 'Primeira avaliação 5 estrelas',
            'status'     => 'aprovada',
        ]);

        $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 5,
            'comentario' => 'Segunda avaliação 5 estrelas',
            'status'     => 'aprovada',
        ]);

        $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 4,
            'comentario' => 'Terceira avaliação 4 estrelas',
            'status'     => 'aprovada',
        ]);

        // Insere uma pendente que NÃO deve entrar no cálculo
        $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 1,
            'comentario' => 'Avaliação pendente não deve afetar a média',
            'status'     => 'pendente',
        ]);

        $stats = $this->avaliacaoModel->getEstatisticasProduto($produtoId);

        $this->assertEquals(3, $stats['total'], 'Total de avaliações aprovadas deve ser 3.');
        $this->assertEquals(4.7, $stats['media'], 'Média ponderada deve ser 4.7.');
        $this->assertEquals(2, $stats['distribuicao'][5], 'Distribuição de 5 estrelas deve ser 2.');
        $this->assertEquals(1, $stats['distribuicao'][4], 'Distribuição de 4 estrelas deve ser 1.');
        $this->assertEquals(0, $stats['distribuicao'][1], 'Distribuição de 1 estrela deve ser 0 (pois estava pendente).');
        $this->assertEquals(100, $stats['recomendacao_percentual'], 'Recomendação deve ser 100% (todas >= 4).');
    }

    public function testDeteccaoCompraVerificada(): void
    {
        $usuarioCompradorId = $this->getUsuarioTeste('comprador_real@teste.com');
        $usuarioNaoCompradorId = $this->getUsuarioTeste('nao_comprador@teste.com');
        $produtoId = $this->getProdutoTeste();

        // Cria pedido pago para o comprador
        $pedidoId = (int) $this->pedidoModel->insert([
            'usuario_id'       => $usuarioCompradorId,
            'valor_total'      => 150.00,
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pago',
            'status'           => 'pago',
            'cep'              => '01310-100',
            'logradouro'       => 'Av Paulista',
            'numero'           => '1000',
            'bairro'           => 'Bela Vista',
            'cidade'           => 'São Paulo',
            'uf'               => 'SP',
        ]);

        $this->pedidoProdutoModel->insert([
            'pedido_id'      => $pedidoId,
            'produto_id'     => $produtoId,
            'quantidade'     => 1,
            'preco_unitario' => 150.00,
        ]);

        // Verifica permissão do comprador
        $statusComprador = $this->avaliacaoModel->usuarioPodeAvaliar($usuarioCompradorId, $produtoId);
        $this->assertTrue($statusComprador['pode_avaliar']);
        $this->assertTrue($statusComprador['comprou'], 'Usuário com pedido pago deve ter comprou = true.');
        $this->assertEquals($pedidoId, $statusComprador['pedido_id']);

        // Verifica permissão do não-comprador
        $statusNaoComprador = $this->avaliacaoModel->usuarioPodeAvaliar($usuarioNaoCompradorId, $produtoId);
        $this->assertTrue($statusNaoComprador['pode_avaliar']);
        $this->assertFalse($statusNaoComprador['comprou'], 'Usuário sem pedidos não deve ter comprou = true.');
    }

    public function testModeracaoStatusAdminETrilhaAuditoria(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        $avaliacaoId = (int) $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 5,
            'titulo'     => 'Excelente para moderar',
            'comentario' => 'Comentário que será aprovado e depois rejeitado.',
            'status'     => 'pendente',
        ]);

        $this->assertGreaterThan(0, $avaliacaoId);

        // 1. Aprovar
        $this->avaliacaoModel->update($avaliacaoId, ['status' => 'aprovada']);
        AuditService::log('status_change', 'avaliacoes', $avaliacaoId, ['status' => 'aprovada'], ['status' => 'pendente'], 1);

        $avAprovada = $this->avaliacaoModel->find($avaliacaoId);
        $this->assertEquals('aprovada', $avAprovada['status']);

        // 2. Rejeitar
        $this->avaliacaoModel->update($avaliacaoId, ['status' => 'rejeitada']);
        AuditService::log('status_change', 'avaliacoes', $avaliacaoId, ['status' => 'rejeitada'], ['status' => 'aprovada'], 1);

        $avRejeitada = $this->avaliacaoModel->find($avaliacaoId);
        $this->assertEquals('rejeitada', $avRejeitada['status']);

        // 3. Excluir
        $this->avaliacaoModel->delete($avaliacaoId);
        $avExcluida = $this->avaliacaoModel->find($avaliacaoId);
        $this->assertNull($avExcluida, 'Avaliação deletada não deve ser encontrada.');
    }

    public function testFiltrosAdminEContadores(): void
    {
        $usuarioId = $this->getUsuarioTeste();
        $produtoId = $this->getProdutoTeste();

        $this->avaliacaoModel->insert([
            'produto_id' => $produtoId,
            'usuario_id' => $usuarioId,
            'nota'       => 5,
            'comentario' => 'Avaliação teste pendente para filtros',
            'status'     => 'pendente',
        ]);

        $contadores = $this->avaliacaoModel->getContadoresStatus();
        $this->assertArrayHasKey('total', $contadores);
        $this->assertArrayHasKey('pendentes', $contadores);
        $this->assertArrayHasKey('aprovadas', $contadores);
        $this->assertArrayHasKey('rejeitadas', $contadores);
        $this->assertArrayHasKey('media_geral', $contadores);
        $this->assertGreaterThanOrEqual(1, $contadores['pendentes']);

        $logsPendentes = $this->avaliacaoModel->getAvaliacoesComFiltros(['status' => 'pendente'], 10);
        $this->assertNotEmpty($logsPendentes);
        foreach ($logsPendentes as $item) {
            $this->assertEquals('pendente', $item['status']);
        }
    }

    public function testHelperRenderEstrelasEBadge(): void
    {
        $htmlEstrelas = renderEstrelas(4.5, 'md', true);
        $this->assertStringContainsString('bi-star-fill', $htmlEstrelas);
        $this->assertStringContainsString('bi-star-half', $htmlEstrelas);
        $this->assertStringContainsString('4,5', $htmlEstrelas);

        $badgeAprovada  = getBadgeStatusAvaliacao('aprovada');
        $badgePendente  = getBadgeStatusAvaliacao('pendente');
        $badgeRejeitada = getBadgeStatusAvaliacao('rejeitada');

        $this->assertStringContainsString('Aprovada', $badgeAprovada);
        $this->assertStringContainsString('Pendente', $badgePendente);
        $this->assertStringContainsString('Rejeitada', $badgeRejeitada);
    }
}
